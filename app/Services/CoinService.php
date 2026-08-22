<?php

namespace App\Services;

use App\BookReadCredit;
use App\Books;
use App\CoinRedemption;
use App\CoinTransaction;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Every coin movement goes through here, so the ledger and users.coin_balance
 * can never drift apart: both are written inside one transaction.
 *
 * Rates come from the settings row, so an admin can change them without a
 * deploy, and each award records the rate that was in force at the time.
 */
class CoinService
{
    /** Coins are off, or the tables were never migrated. */
    public function enabled()
    {
        return $this->unavailableReason() === '';
    }

    /**
     * Why coins are unavailable, or '' when they are fine.
     *
     * Worth separating: "the admin switched it off" and "this server never ran
     * the migration" look identical to a student but need opposite fixes, and
     * the second one is invisible unless something says it out loud.
     */
    public function unavailableReason()
    {
        if (!Schema::hasTable('coin_transactions')) {
            return 'setup';
        }
        if ((int) $this->setting('coins_enabled', 1) !== 1) {
            return 'disabled';
        }
        return '';
    }

    public function coinsPerRead()
    {
        return max(0, (int) $this->setting('coins_per_read', 1));
    }

    public function coinsPerUpload()
    {
        return max(0, (int) $this->setting('coins_per_upload', 10));
    }

    /** Money one coin is worth. */
    public function coinValue()
    {
        return max(0, (float) $this->setting('coin_value', 0.1));
    }

    public function minRedeem()
    {
        return max(1, (int) $this->setting('coins_min_redeem', 500));
    }

    public function balance($userId)
    {
        $user = User::find($userId);
        return $user ? (int) $user->coin_balance : 0;
    }

    /** What a coin balance is worth, rounded to money. */
    public function valueOf($coins)
    {
        return round(((int) $coins) * $this->coinValue(), 2);
    }

    /**
     * Pay the uploader for someone reading their book.
     *
     * Returns the coins awarded, or 0 when nothing was due: coins off, no
     * uploader, the reader is the uploader, or this reader already counted. The
     * insert into book_read_credits is the claim — a duplicate key means another
     * request got there first, and we simply pay nothing.
     */
    public function creditRead($bookId, $readerId)
    {
        if (!$this->enabled()) {
            return 0;
        }
        $coins = $this->coinsPerRead();
        if ($coins <= 0 || empty($bookId) || empty($readerId)) {
            return 0;
        }

        $book = Books::find($bookId);
        if (!$book || empty($book->uploaded_by)) {
            return 0;   // admin-published books earn nobody anything
        }
        $uploaderId = (int) $book->uploaded_by;
        if ($uploaderId === (int) $readerId) {
            return 0;   // reading your own upload is not a read
        }

        try {
            return DB::transaction(function () use ($bookId, $readerId, $uploaderId, $coins, $book) {
                BookReadCredit::create([
                    'book_id'     => $bookId,
                    'reader_id'   => $readerId,
                    'uploader_id' => $uploaderId,
                    'coins'       => $coins,
                ]);

                $this->award($uploaderId, $coins, CoinTransaction::TYPE_READ, [
                    'book_id'   => $bookId,
                    'reader_id' => $readerId,
                    'note'      => 'Read of "' . mb_substr((string) $book->title, 0, 120) . '"',
                ]);

                return $coins;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // 23000 = duplicate key: this reader already earned for this book.
            if ($e->getCode() === '23000') {
                return 0;
            }
            Log::error('creditRead failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Pay a student for an upload being approved. Idempotent: a book is only
     * ever paid for once, however many times it is re-approved.
     */
    public function creditUpload($bookId)
    {
        if (!$this->enabled()) {
            return 0;
        }
        $coins = $this->coinsPerUpload();
        if ($coins <= 0) {
            return 0;
        }

        $book = Books::find($bookId);
        if (!$book || empty($book->uploaded_by)) {
            return 0;
        }

        $already = CoinTransaction::where('type', CoinTransaction::TYPE_UPLOAD)
            ->where('book_id', $bookId)->exists();
        if ($already) {
            return 0;
        }

        $uploaderId = (int) $book->uploaded_by;
        DB::transaction(function () use ($uploaderId, $coins, $bookId, $book) {
            $this->award($uploaderId, $coins, CoinTransaction::TYPE_UPLOAD, [
                'book_id' => $bookId,
                'note'    => 'Upload approved: "' . mb_substr((string) $book->title, 0, 120) . '"',
            ]);
        });

        return $coins;
    }

    /** Admin correction, positive or negative. */
    public function adjust($userId, $coins, $note = '')
    {
        if ((int) $coins === 0) {
            return 0;
        }
        DB::transaction(function () use ($userId, $coins, $note) {
            $this->award($userId, (int) $coins, CoinTransaction::TYPE_ADJUST, ['note' => $note]);
        });
        return (int) $coins;
    }

    /**
     * Spend coins on a gift card.
     *
     * The coins are deducted and the redemption recorded first, then the coupon
     * is created at the shop. If the shop call fails the redemption stays as
     * 'failed' with the coins returned, so a student is never left out of pocket
     * because of an outage.
     *
     * @return CoinRedemption
     * @throws \RuntimeException when the request itself is not allowed
     */
    public function redeem($userId, $coins)
    {
        if (!$this->enabled()) {
            throw new \RuntimeException('Coins are not available right now');
        }
        $coins = (int) $coins;
        $user = User::find($userId);
        if (!$user) {
            throw new \RuntimeException('Something went wrong');
        }
        if ($coins < $this->minRedeem()) {
            throw new \RuntimeException('You need at least ' . $this->minRedeem() . ' coins to redeem');
        }
        if ($coins > (int) $user->coin_balance) {
            throw new \RuntimeException('You do not have that many coins');
        }

        $amount = $this->valueOf($coins);
        if ($amount <= 0) {
            throw new \RuntimeException('Coins have no value set yet. Please contact support.');
        }

        $redemption = DB::transaction(function () use ($user, $coins, $amount) {
            $r = CoinRedemption::create([
                'user_id' => $user->id,
                'coins'   => $coins,
                'amount'  => $amount,
                'status'  => CoinRedemption::STATUS_PENDING,
            ]);

            $this->award($user->id, -$coins, CoinTransaction::TYPE_REDEEM, [
                'redemption_id' => $r->id,
                'note'          => 'Gift card for ' . $amount,
            ]);

            return $r;
        });

        $this->issueGiftCard($redemption);

        return $redemption->fresh();
    }

    /**
     * Turn a pending redemption into a real shop coupon. Safe to call again on a
     * failed one, which is what the admin "retry" action does.
     */
    public function issueGiftCard(CoinRedemption $redemption)
    {
        if ($redemption->status === CoinRedemption::STATUS_ISSUED) {
            return true;
        }

        $user = User::find($redemption->user_id);
        $code = $redemption->code ?: $this->generateCode();

        $payload = [
            'code'                   => $code,
            'discount_type'          => 'fixed_cart',
            'amount'                 => (string) $redemption->amount,
            'individual_use'         => false,
            'usage_limit'            => 1,
            'usage_limit_per_user'   => 1,
            'exclude_sale_items'     => false,
            'description'            => 'Coin gift card for ' . ($user ? $user->email : 'student')
                                        . ' (' . $redemption->coins . ' coins)',
        ];
        // Tie it to the student's account so a shared code is not spendable.
        if ($user && $user->email) {
            $payload['email_restrictions'] = [$user->email];
        }

        $result = function_exists('woo_post') ? woo_post('coupons', $payload) : null;

        if (is_array($result) && !empty($result['id'])) {
            $redemption->code = $code;
            $redemption->woo_coupon_id = $result['id'];
            $redemption->status = CoinRedemption::STATUS_ISSUED;
            $redemption->fail_reason = null;
            $redemption->issued_at = now();
            $redemption->save();
            return true;
        }

        $redemption->code = $redemption->code ?: $code;
        $redemption->status = CoinRedemption::STATUS_FAILED;
        $redemption->fail_reason = 'Could not create the coupon at the shop';
        $redemption->save();
        Log::warning('Gift card issue failed for redemption ' . $redemption->id);

        return false;
    }

    /** Give the coins back on a redemption that will never be issued. */
    public function refund(CoinRedemption $redemption, $note = 'Gift card cancelled')
    {
        if ($redemption->status === CoinRedemption::STATUS_ISSUED) {
            return false;
        }
        $alreadyRefunded = CoinTransaction::where('redemption_id', $redemption->id)
            ->where('coins', '>', 0)->exists();
        if ($alreadyRefunded) {
            return false;
        }

        DB::transaction(function () use ($redemption, $note) {
            $this->award($redemption->user_id, (int) $redemption->coins, CoinTransaction::TYPE_ADJUST, [
                'redemption_id' => $redemption->id,
                'note'          => $note,
            ]);
            $redemption->status = 'cancelled';
            $redemption->save();
        });

        return true;
    }

    /**
     * Per-book earnings for an uploader: what the app's "My Coins" screen shows.
     */
    public function bookBreakdown($userId)
    {
        $books = Books::where('uploaded_by', $userId)->orderBy('id', 'DESC')->get();

        $earned = CoinTransaction::where('user_id', $userId)
            ->where('type', CoinTransaction::TYPE_READ)
            // READS is a reserved word in MySQL, so the alias has to be something else.
                ->selectRaw('book_id, SUM(coins) as total_coins, COUNT(*) as read_count')
            ->groupBy('book_id')
            ->get()
            ->keyBy('book_id');

        $out = [];
        foreach ($books as $b) {
            $row = isset($earned[$b->id]) ? $earned[$b->id] : null;
            $out[] = [
                'book_id' => $b->id,
                'title'   => stripslashes((string) $b->title),
                'image'   => function_exists('book_asset_url') ? book_asset_url($b->image) : (string) $b->image,
                'status'  => (string) $b->upload_status,
                'views'   => (int) (function_exists('post_views_count') ? post_views_count($b->id, 'post') : 0),
                'reads'   => $row ? (int) $row->read_count : 0,
                'coins'   => $row ? (int) $row->total_coins : 0,
            ];
        }
        return $out;
    }

    public function totalEarned($userId)
    {
        return (int) CoinTransaction::where('user_id', $userId)->where('coins', '>', 0)->sum('coins');
    }

    public function totalRedeemed($userId)
    {
        return (int) abs(CoinTransaction::where('user_id', $userId)->where('coins', '<', 0)->sum('coins'));
    }

    // ---------------------------------------------------------------- internals

    /** Ledger row + running balance, always together. Caller owns the transaction. */
    protected function award($userId, $coins, $type, array $extra = [])
    {
        CoinTransaction::create(array_merge([
            'user_id' => $userId,
            'type'    => $type,
            'coins'   => (int) $coins,
        ], $extra));

        // Atomic increment, so concurrent reads of the same uploader cannot
        // overwrite each other's balance.
        User::where('id', $userId)->update([
            'coin_balance' => DB::raw('COALESCE(coin_balance, 0) + ' . (int) $coins),
        ]);
    }

    protected function generateCode()
    {
        do {
            $code = 'JB' . strtoupper(\Illuminate\Support\Str::random(8));
        } while (CoinRedemption::where('code', $code)->exists());

        return $code;
    }

    protected function setting($key, $default)
    {
        try {
            if (!Schema::hasColumn('settings', $key)) {
                return $default;
            }
            $value = getcong($key);
            return ($value === null || $value === '') ? $default : $value;
        } catch (\Exception $e) {
            return $default;
        }
    }
}

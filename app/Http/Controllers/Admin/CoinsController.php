<?php

namespace App\Http\Controllers\Admin;

use App\Books;
use App\CoinRedemption;
use App\CoinTransaction;
use App\Services\CoinService;
use App\Settings;
use App\User;
use Auth;
use Illuminate\Http\Request;

/**
 * Admin side of the coins feature: the earning rates, who holds what, the full
 * ledger, and the gift cards students have cashed out for.
 */
class CoinsController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Overview: totals, the top earners, and the latest movements. */
    public function index()
    {
        $coins = new CoinService();

        $stats = array(
            'in_circulation' => (int) User::sum('coin_balance'),
            'earned'         => (int) CoinTransaction::where('coins', '>', 0)->sum('coins'),
            'redeemed'       => (int) abs(CoinTransaction::where('coins', '<', 0)->sum('coins')),
            'pending_cards'  => CoinRedemption::whereIn('status', array('pending', 'failed'))->count(),
        );
        $stats['liability'] = $coins->valueOf($stats['in_circulation']);

        $earners = User::where('coin_balance', '>', 0)
            ->orderBy('coin_balance', 'DESC')->paginate(20);

        $recent = CoinTransaction::orderBy('id', 'DESC')->take(25)->get();

        $page_title = 'Coins';
        return view('admin.pages.coins.index', compact('page_title', 'stats', 'earners', 'recent', 'coins'));
    }

    /** One student: their books, their earnings, their cards. */
    public function user($id)
    {
        $user = User::findOrFail($id);
        $coins = new CoinService();

        $books = $coins->bookBreakdown($id);
        $ledger = CoinTransaction::where('user_id', $id)->orderBy('id', 'DESC')->paginate(30);
        $cards = CoinRedemption::where('user_id', $id)->orderBy('id', 'DESC')->get();

        $page_title = 'Coins — ' . $user->name;
        return view('admin.pages.coins.user', compact('page_title', 'user', 'books', 'ledger', 'cards', 'coins'));
    }

    /** Hand-adjust a balance, for corrections and goodwill. */
    public function adjust(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $amount = (int) $request->input('coins', 0);
        $note = trim((string) $request->input('note', ''));

        if ($amount === 0) {
            \Session::flash('flash_message', 'Enter a non-zero number of coins.');
            return redirect()->back();
        }
        // Never push a balance negative — that would look like a debt the
        // student has no way to clear.
        if ($amount < 0 && abs($amount) > (int) $user->coin_balance) {
            \Session::flash('flash_message', 'That would take the balance below zero.');
            return redirect()->back();
        }

        (new CoinService())->adjust($user->id, $amount, $note ?: 'Adjusted by admin');
        \Session::flash('flash_message', 'Balance adjusted by ' . $amount . ' coins.');
        return redirect()->back();
    }

    /** Every cash-out, newest first. */
    public function redemptions(Request $request)
    {
        $query = CoinRedemption::orderBy('id', 'DESC');
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('s')) {
            $query->where('code', 'LIKE', '%' . $request->input('s') . '%');
        }
        $list = $query->paginate(25);
        $list->appends(\Request::except('page'));

        $page_title = 'Gift cards';
        return view('admin.pages.coins.redemptions', compact('page_title', 'list'));
    }

    /** Try the shop again for a card that failed to mint. */
    public function retry($id)
    {
        $redemption = CoinRedemption::findOrFail($id);
        $ok = (new CoinService())->issueGiftCard($redemption);

        \Session::flash('flash_message', $ok
            ? 'Gift card issued: ' . $redemption->fresh()->code
            : 'The shop did not accept it. Check the WooCommerce credentials and the log.');
        return redirect()->back();
    }

    /** Give the coins back and drop the card. */
    public function cancel($id)
    {
        $redemption = CoinRedemption::findOrFail($id);
        $ok = (new CoinService())->refund($redemption);

        \Session::flash('flash_message', $ok
            ? 'Coins returned to the student.'
            : 'Already issued or already refunded — nothing to return.');
        return redirect()->back();
    }

    /** The rates. */
    public function settings()
    {
        if (Auth::User()->usertype != "Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }
        $settings = Settings::findOrFail('1');
        $page_title = 'Coin settings';
        return view('admin.pages.coins.settings', compact('page_title', 'settings'));
    }

    public function update_settings(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $settings = Settings::findOrFail('1');
        $settings->coins_enabled    = (int) $request->input('coins_enabled', 0);
        $settings->coins_per_read   = max(0, (int) $request->input('coins_per_read', 0));
        $settings->coins_per_upload = max(0, (int) $request->input('coins_per_upload', 0));
        $settings->coin_value       = max(0, (float) $request->input('coin_value', 0));
        $settings->coins_min_redeem = max(1, (int) $request->input('coins_min_redeem', 1));
        $settings->save();

        \Session::flash('flash_message', 'Coin settings saved.');
        return redirect('admin/coin_settings');
    }
}

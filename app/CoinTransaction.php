<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * One movement of coins. Earnings are positive, redemptions negative; the sum
 * over a user is their balance, and users.coin_balance mirrors it.
 */
class CoinTransaction extends Model
{
    protected $table = 'coin_transactions';

    protected $fillable = [
        'user_id', 'type', 'coins', 'book_id', 'reader_id', 'redemption_id', 'note',
    ];

    const TYPE_READ   = 'read';
    const TYPE_UPLOAD = 'upload';
    const TYPE_REDEEM = 'redeem';
    const TYPE_ADJUST = 'adjust';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book()
    {
        return $this->belongsTo(Books::class, 'book_id');
    }
}

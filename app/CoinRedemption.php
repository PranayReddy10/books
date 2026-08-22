<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * A cash-out: coins spent for a shop gift card. The code is a real WooCommerce
 * coupon, so it works at checkout on the shop site.
 */
class CoinRedemption extends Model
{
    protected $table = 'coin_redemptions';

    protected $fillable = [
        'user_id', 'coins', 'amount', 'code', 'status',
        'fail_reason', 'woo_coupon_id', 'issued_at',
    ];

    protected $casts = ['issued_at' => 'datetime'];

    const STATUS_PENDING = 'pending';
    const STATUS_ISSUED  = 'issued';
    const STATUS_FAILED  = 'failed';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

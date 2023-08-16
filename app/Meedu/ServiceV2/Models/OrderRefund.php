<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRefund extends Model
{
    protected $table = 'order_refund';

    protected $fillable = [
        'order_id', 'user_id', 'payment', 'total_amount',
        'refund_no', 'amount', 'reason', 'is_local', 'status', 'success_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}

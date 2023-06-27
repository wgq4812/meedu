<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Models;

use App\Constant\TableConstant;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = TableConstant::TABLE_ORDERS;

    protected $fillable = [
        'user_id', 'charge', 'status', 'order_id', 'payment',
        'payment_method', 'is_refund', 'last_refund_at',
    ];
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Models;

use App\Constant\TableConstant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $table = TableConstant::TABLE_ORDERS;

    protected $fillable = [
        'user_id', 'charge', 'status',
        // 订单编号
        'order_id',
        // 支付信息
        'payment', 'payment_method',
        // 退款信息
        'is_refund', 'last_refund_at',

        'created_at',
    ];

    public function goods()
    {
        return $this->hasMany(OrderGoods::class, 'oid');
    }
}

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

class OrderGoods extends Model
{
    use HasFactory;

    protected $table = TableConstant::TABLE_ORDER_GOODS;

    protected $fillable = [
        // 订单表orders的id
        'oid',
        // 学员ID
        'user_id',
        // 商品基础信息
        'goods_id', 'goods_type', 'goods_name', 'goods_thumb', 'goods_charge', 'goods_ori_charge',

        // 购买数量和价格
        'num', 'charge',

        // todo 即将废弃
        'order_id',
    ];
}

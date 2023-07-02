<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Database\Factories\Meedu\ServiceV2\Models;

use App\Constant\FrontendConstant;
use App\Services\Member\Models\User;
use App\Meedu\ServiceV2\Models\Order;
use App\Meedu\ServiceV2\Models\OrderGoods;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderGoodsFactory extends Factory
{
    protected $model = OrderGoods::class;

    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'oid' => function () {
                return Order::factory()->create()->id;
            },
            'goods_id' => mt_rand(0, 100),
            'goods_type' => $this->faker->randomElement([
                FrontendConstant::ORDER_TYPE_ROLE,
                FrontendConstant::ORDER_TYPE_COURSE,
            ]),
            'goods_name' => mb_substr($this->faker->title, 0, 122),
            'goods_thumb' => $this->faker->imageUrl(),
            'goods_charge' => 199,
            'goods_ori_charge' => 299,
            'num' => 1,
            'charge' => mt_rand(0, 100),

            // 废弃
            'order_id' => '',
        ];
    }
}

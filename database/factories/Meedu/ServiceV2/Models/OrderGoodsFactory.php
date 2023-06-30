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
            'order_id' => '',
            'oid' => function () {
                return Order::factory()->create()->id;
            },
            'goods_type' => $this->faker->randomElement([
                FrontendConstant::ORDER_TYPE_ROLE,
                FrontendConstant::ORDER_TYPE_COURSE,
            ]),
            'goods_id' => mt_rand(0, 100),
            'num' => 1,
            'charge' => mt_rand(0, 100),
        ];
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Database\Factories\Meedu\ServiceV2\Models;

use App\Constant\FrontendConstant;
use App\Meedu\ServiceV2\Models\OrderPaidRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderPaidRecordFactory extends Factory
{
    protected $model = OrderPaidRecord::class;

    public function definition()
    {
        return [
            'user_id' => 0,
            'order_id' => 0,
            'paid_total' => mt_rand(0, 100),
            'paid_type' => $this->faker->randomElement([
                FrontendConstant::ORDER_PAID_TYPE_DEFAULT,
                FrontendConstant::ORDER_PAID_TYPE_PROMO_CODE,
                FrontendConstant::ORDER_PAID_TYPE_HAND,
            ]),
            'paid_type_id' => 0,
        ];
    }
}

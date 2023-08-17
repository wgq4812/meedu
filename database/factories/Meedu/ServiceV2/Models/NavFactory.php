<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Database\Factories\Meedu\ServiceV2\Models;

use App\Constant\FrontendConstant;
use App\Meedu\ServiceV2\Models\Nav;
use Illuminate\Database\Eloquent\Factories\Factory;

class NavFactory extends Factory
{
    protected $model = Nav::class;

    public function definition()
    {
        return [
            'sort' => $this->faker->numberBetween(0, 100),
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'platform' => $this->faker->randomElement([
                FrontendConstant::NAV_PLATFORM_H5,
                FrontendConstant::NAV_PLATFORM_PC,
            ]),
            'parent_id' => 0,
        ];
    }
}

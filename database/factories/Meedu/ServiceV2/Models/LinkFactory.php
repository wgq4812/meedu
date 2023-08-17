<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Database\Factories\Meedu\ServiceV2\Models;

use App\Meedu\ServiceV2\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    protected $model = Link::class;

    public function definition()
    {
        return [
            'sort' => $this->faker->numberBetween(0, 100),
            'name' => $this->faker->name,
            'url' => $this->faker->url,
        ];
    }
}

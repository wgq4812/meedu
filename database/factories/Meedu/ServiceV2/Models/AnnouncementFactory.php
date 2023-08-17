<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Database\Factories\Meedu\ServiceV2\Models;

use Carbon\Carbon;
use App\Models\Administrator;
use App\Meedu\ServiceV2\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'admin_id' => function () {
                return Administrator::factory()->create()->id;
            },
            'announcement' => $this->faker->title,
            'created_at' => Carbon::now(),
        ];
    }
}

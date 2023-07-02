<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Database\Factories\Meedu\ServiceV2\Models;

use Carbon\Carbon;
use App\Meedu\ServiceV2\Models\UserPromoCodeRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPromoCodeRecordFactory extends Factory
{
    protected $model = UserPromoCodeRecord::class;

    public function definition()
    {
        return [
            'user_id' => 0,
            'code_id' => 0,
            'order_id' => 0,
            'original_amount' => 0,
            'discount' => 0,
            'created_at' => Carbon::now()->toDateTimeLocalString(),
        ];
    }
}

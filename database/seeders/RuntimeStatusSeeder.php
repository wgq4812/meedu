<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Constant\RuntimeConstant;
use App\Meedu\ServiceV2\Models\RuntimeStatus;

class RuntimeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            RuntimeConstant::SYSTEM_VERSION => '',
            RuntimeConstant::SYSTEM_SCHEDULE => '',
            RuntimeConstant::SYSTEM_QUEUE => '',

            RuntimeConstant::TENCENT_VOD_SECRET => '',
            RuntimeConstant::TENCENT_VOD_APP => '',
            RuntimeConstant::TENCENT_VOD_EVENT => '',
            RuntimeConstant::TENCENT_VOD_DOMAIN => '',
            RuntimeConstant::TENCENT_VOD_DOMAIN_KEY => '',
            RuntimeConstant::TENCENT_VOD_TRANSCODE_TASK_SIMPLE => '',
        ];

        $runtime = RuntimeStatus::query()->get()->pluck('status', 'name')->toArray();
        $insertData = [];
        $now = Carbon::now()->toDateTimeLocalString();

        foreach ($data as $name => $status) {
            if (isset($runtime[$name])) {
                continue;
            }
            $insertData[] = [
                'name' => $name,
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $insertData && RuntimeStatus::insert($insertData);
    }
}

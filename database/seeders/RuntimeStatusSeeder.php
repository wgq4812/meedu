<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Constant\RuntimeConstant as RC;
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
            RC::SYSTEM_VERSION => '',
            RC::SYSTEM_SCHEDULE => '',

            RC::TENCENT_VOD_SECRET => '',
            RC::TENCENT_VOD_APP => '',
            RC::TENCENT_VOD_EVENT => '',
            RC::TENCENT_VOD_DOMAIN => '',
            RC::TENCENT_VOD_DOMAIN_KEY => '',
            RC::TENCENT_VOD_TRANSCODE_TASK_SIMPLE => '',
            RC::TENCENT_VOD_CDN_KEY => '',

            RC::ALI_VOD_SECRET => '',
            RC::ALI_VOD_APP => '',
            RC::ALI_VOD_EVENT => '',
            RC::ALI_VOD_DOMAIN => '',
            RC::ALI_VOD_TRANSCODE => '',
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

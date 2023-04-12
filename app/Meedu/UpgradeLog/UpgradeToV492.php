<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\UpgradeLog;

use App\Services\Base\Model\AppConfig;

class UpgradeToV492
{
    public static function handle()
    {
        self::deleteConfig();
    }

    public static function deleteConfig()
    {
        AppConfig::query()
            ->whereIn('key', [
                'meedu.services.amap.key',
            ])
            ->delete();
    }
}

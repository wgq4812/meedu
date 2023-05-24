<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\UpgradeLog;

use App\Services\Base\Model\AppConfig;
use App\Models\AdministratorPermission;

class UpgradeToV500
{
    public static function handle()
    {
        self::deleteConfig();
        self::deletePermissions();
    }

    public static function deleteConfig()
    {
        AppConfig::query()
            ->whereIn('key', [
                'meedu.services.amap.key',
            ])
            ->delete();
    }

    public static function deletePermissions()
    {
        AdministratorPermission::query()
            ->whereIn('slug', [
                'administrator_role.edit',
            ])
            ->delete();
    }
}

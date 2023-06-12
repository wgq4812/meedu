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
        self::updateConfig();
    }

    public static function deleteConfig()
    {
        AppConfig::query()
            ->whereIn('key', [
                'meedu.services.amap.key',

                // 七牛云配置
                'filesystems.disks.qiniu.domains.default',
                'filesystems.disks.qiniu.domains.https',
                'filesystems.disks.qiniu.access_key',
                'filesystems.disks.qiniu.secret_key',
                'filesystems.disks.qiniu.bucket',
            ])
            ->delete();
    }

    public static function updateConfig()
    {
        AppConfig::query()->where('key', 'meedu.upload.image.disk')->update([
            'option_value' => json_encode([
                [
                    'title' => '本地',
                    'key' => 'public',
                ],
                [
                    'title' => '阿里云OSS',
                    'key' => 'oss',
                ],
                [
                    'title' => '腾讯云COS',
                    'key' => 'cos',
                ],
            ]),
        ]);
    }

    public static function deletePermissions()
    {
        AdministratorPermission::query()
            ->whereIn('slug', [
                'administrator_role.edit',
                'media.video.store',//视频上传后的本地存储
            ])
            ->delete();
    }
}

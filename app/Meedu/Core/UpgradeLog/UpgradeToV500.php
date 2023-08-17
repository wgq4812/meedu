<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Core\UpgradeLog;

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

                // 云片短信
                'sms.gateways.yunpian.api_key',
                'sms.gateways.yunpian.template.password_reset',
                'sms.gateways.yunpian.template.register',
                'sms.gateways.yunpian.template.mobile_bind',
                'sms.gateways.yunpian.template.login',
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

        AppConfig::query()->where('key', 'meedu.system.sms')->update([
            'option_value' => json_encode([
                [
                    'title' => '阿里云',
                    'key' => 'aliyun',
                ],
                [
                    'title' => '腾讯云',
                    'key' => 'tencent',
                ],
            ]),
        ]);

        AppConfig::query()->where('key', 'meedu.payment.alipay.enabled')->update(['name' => '支付宝-电脑端']);
        AppConfig::query()->where('key', 'meedu.payment.wechat.enabled')->update(['name' => '微信支付-电脑端']);
        AppConfig::query()->where('key', 'meedu.payment.wechat-jsapi.enabled')->update(['name' => '微信支付-微信公众号']);
        AppConfig::query()->where('key', 'meedu.payment.handPay.enabled')->update(['name' => '手动打款-电脑端']);
    }

    public static function deletePermissions()
    {
        AdministratorPermission::query()
            ->whereIn('slug', [
                'administrator_role.edit',
                'media.video.store',//视频上传后的本地存储

                'slider',
                'slider.store',
                'slider.update',
                'slider.destroy',
            ])
            ->delete();
    }
}

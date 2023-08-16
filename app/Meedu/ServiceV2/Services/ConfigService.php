<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

class ConfigService implements ConfigServiceInterface
{
    public function getSuperAdministratorSlug(): string
    {
        return config('meedu.administrator.super_slug') ?? '';
    }

    public function getEnabledSocialiteApps(): array
    {
        return collect(config('meedu.member.socialite', []))->filter(function ($item) {
            return (int)($item['enabled'] ?? 0) == 1;
        })->toArray();
    }

    public function getLoginLimitRule(): int
    {
        return (int)config('meedu.system.login.limit.rule', 0);
    }

    public function isCloseDeprecatedApi(): bool
    {
        return (bool)config('meedu.system.close_deprecated_api');
    }

    public function getMpWechatScanLoginAlert(): string
    {
        return config('meedu.mp_wechat.scan_login_alert') ?? '';
    }

    public function getTencentFaceConfig(): array
    {
        return config('tencent.face') ?? [];
    }

    public function enabledFaceVerify(): bool
    {
        return (int)config('meedu.member.enabled_face_verify') === 1;
    }

    public function getVideoDefaultService(): string
    {
        return config('meedu.upload.video.default_service') ?? '';
    }

    public function getApiUrl(): string
    {
        return config('app.url') ?? '';
    }

    public function getPCUrl(): string
    {
        return config('meedu.system.pc_url') ?? '';
    }

    public function getH5Url(): string
    {
        return config('meedu.system.h5_url') ?? '';
    }

    public function getLogo(): string
    {
        return config('meedu.system.logo') ?? '';
    }

    public function getAliCallbackKey(): string
    {
        return config('meedu.upload.video.aliyun.callback_key') ?? '';
    }

    public function getTencentVodCallbackKey(): string
    {
        return config('tencent.vod.callback_key') ?? '';
    }

    public function getTencentVodConfig(): array
    {
        return config('tencent.vod') ?? [];
    }

    public function getTencentVodPlayKey(): string
    {
        return config('meedu.system.player.tencent_play_key') ?? '';
    }

    public function getAliVodConfig(): array
    {
        return config('meedu.upload.video.aliyun');
    }

    public function isEnabledRedisCache(): bool
    {
        return config('cache.default') === 'redis';
    }

    public function isEnabledRedisQueue(): bool
    {
        return config('queue.default') === 'redis';
    }

    public function isEnvTest(): bool
    {
        return config('app.env') === 'testing';
    }

    public function getAppName(): string
    {
        return config('app.name') ?? '';
    }

    public function getICP(): string
    {
        return config('meedu.system.icp', '');
    }

    public function getICPLink(): string
    {
        return config('meedu.system.icp_link');
    }

    public function getICP2(): string
    {
        return config('meedu.system.icp2', '');
    }

    public function getICP2Link(): string
    {
        return config('meedu.system.icp2_link', '');
    }

    public function getPlayerCover(): string
    {
        return config('meedu.system.player_thumb') ?? '';
    }

    public function getPlayerBulletSecret(): array
    {
        $playerConfig = config('meedu.system.player');

        return [
            'enabled' => $playerConfig['enabled_bullet_secret'],
            'text' => $playerConfig['bullet_secret']['text'],
            'size' => $playerConfig['bullet_secret']['size'],
            'color' => $playerConfig['bullet_secret']['color'],
            'opacity' => $playerConfig['bullet_secret']['opacity'],
        ];
    }

    public function enabledMustBindMobile(): int
    {
        return (int)config('meedu.member.enabled_mobile_bind_alert');
    }

    public function enabledQQLogin(): int
    {
        return (int)config('meedu.member.socialite.qq.enabled');
    }

    public function enabledWechatScanLogin(): int
    {
        return (int)config('meedu.mp_wechat.enabled_scan_login');
    }

    public function enabledWechatOAUTHLogin(): int
    {
        return (int)config('meedu.mp_wechat.enabled_oauth_login');
    }

    public function getCredit1Register(): int
    {
        return (int)config('meedu.member.credit1.register');
    }

    public function getCredit1WatchedCourse(): int
    {
        return (int)config('meedu.member.credit1.watched_course');
    }

    public function getCredit1WatchedVideo(): int
    {
        return (int)config('meedu.member.credit1.watched_video');
    }

    public function getCredit1CreatedPaidOrder(): string
    {
        return (string)config('meedu.member.credit1.paid_order');
    }

    public function paymentsStatus(): array
    {
        $payments = $this->payments();
        $data = [];
        foreach ($payments as $key => $item) {
            $data[] = [
                'payment' => $key,
                'status' => $item['enabled'],
            ];
        }
        return $data;
    }

    public function payments(): array
    {
        return config('meedu.payment');
    }

    public function enabledPayments(): array
    {
        return collect($this->payments())->map(function ($item) {
            return [
                'enabled' => (int)$item['enabled'],
                'name' => $item['name'],
                'logo' => url($item['logo']),
                'sign' => $item['sign'],
            ];
        })->toArray();
    }

    public function getHandPayDesc(): string
    {
        return config('meedu.payment.handPay.introduction') ?? '';
    }

    public function getAlipayConfig(): array
    {
        $config = config('pay.alipay');
        $config['notify_url'] = route('payment.callback', ['alipay']);
        return $config;
    }

    public function getWechatPayConfig(): array
    {
        $config = config('pay.wechat');
        $config['notify_url'] = route('payment.callback', ['wechat']);
        return $config;
    }

    public function getOrderHandler(): array
    {
        return config('meedu.orderHandler');
    }

    public function getMemberProtocol(): string
    {
        return config('meedu.member.protocol') ?? '';
    }

    public function getMemberPrivateProtocol(): string
    {
        return config('meedu.member.private_protocol') ?? '';
    }

    public function getAboutUs(): string
    {
        return config('meedu.aboutus') ?? '';
    }


}

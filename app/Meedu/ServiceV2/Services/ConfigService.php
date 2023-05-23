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
}

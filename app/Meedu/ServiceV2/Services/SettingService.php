<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Meedu\Core\Setting;

class SettingService implements SettingServiceInterface
{
    private $settingSource;

    public function __construct(Setting $setting)
    {
        $this->settingSource = $setting;
    }

    public function save(array $config)
    {
        $this->settingSource->put($config);
    }

    public function saveTencentVodDomainKey(string $key)
    {
        $this->settingSource->put(['meedu.system.player.tencent_play_key' => $key]);
    }

    public function saveTencentVodDomainAndKey(string $domain, string $key)
    {
        $this->settingSource->put([
            'tencent.vod.domain' => $domain,
            'meedu.system.player.tencent_play_key' => $key,
        ]);
    }

    public function saveTencentVodCdnKey(string $key)
    {
        $this->settingSource->put(['tencent.vod.cdn_key' => $key]);
    }


    public function saveTencentVodAppId(string $appId)
    {
        $this->settingSource->put(['tencent.vod.app_id' => $appId]);
    }

    public function saveTencentVodCallbackKey(string $key)
    {
        $this->settingSource->put(['tencent.vod.callback_key' => $key]);
    }

    public function saveAliVodCallbackKey(string $key)
    {
        $this->settingSource->put(['meedu.upload.video.aliyun.callback_key' => $key]);
    }
}

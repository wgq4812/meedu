<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface SettingServiceInterface
{
    public function save(array $config);

    public function saveTencentVodDomainKey(string $key);

    public function saveTencentVodDomainAndKey(string $domain, string $key);

    public function saveTencentVodCallbackKey(string $key);


    public function saveTencentVodAppId(string $appId);
}

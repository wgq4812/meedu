<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Constant\RuntimeConstant as RC;
use App\Meedu\ServiceV2\Dao\RuntimeStatusDaoInterface;

class RuntimeStatusStatusService implements RuntimeStatusServiceInterface
{
    private $rsDao;

    public function __construct(RuntimeStatusDaoInterface $rsDao)
    {
        $this->rsDao = $rsDao;
    }

    public function setTencentVodDomainKey(bool $isOk)
    {
        $this->rsDao->save(RC::TENCENT_VOD_DOMAIN_KEY, $isOk ? RC::STATUS_OK : '');
    }

    public function setTencentVodCdnKey(bool $isOk)
    {
        $this->rsDao->save(RC::TENCENT_VOD_CDN_KEY, $isOk ? RC::STATUS_OK : '');
    }

    public function setTencentVodDomain(string $domain)
    {
        $this->rsDao->save(RC::TENCENT_VOD_DOMAIN, $domain);
        $this->setTencentVodDomainKey(true);
    }

    public function setTencentVodApp(string $appId)
    {
        $this->rsDao->save(RC::TENCENT_VOD_APP, $appId);
    }

    public function setTencentVodSecret(bool $isOk)
    {
        $this->rsDao->save(RC::TENCENT_VOD_SECRET, $isOk ? RC::STATUS_OK : '');
    }

    public function setTencentVodEvent(string $url)
    {
        $this->rsDao->save(RC::TENCENT_VOD_EVENT, $url);
    }

    public function setTencentVodTranscodeSimpleTask(bool $isOk)
    {
        $this->rsDao->save(RC::TENCENT_VOD_TRANSCODE_TASK_SIMPLE, $isOk ? RC::STATUS_OK : '');
    }

    public function chunks(array $names): array
    {
        return $this->rsDao->nameChunks($names);
    }

    public function tencentVodStatus(): array
    {
        return $this->rsDao->nameChunks(RC::TENCENT_VOD_NAMES);
    }

    public function aliVodStatus(): array
    {
        return $this->rsDao->nameChunks(RC::ALI_VOD_NAMES);
    }

    public function setAliVodSecret(bool $isOk)
    {
        $this->rsDao->save(RC::ALI_VOD_SECRET, $isOk ? RC::STATUS_OK : '');
    }

    public function setAliVodDomain(string $domain)
    {
        $this->rsDao->save(RC::ALI_VOD_DOMAIN, $domain);
    }

    public function setAliVodEvent(string $url)
    {
        $this->rsDao->save(RC::ALI_VOD_EVENT, $url);
    }

    public function setAliVodTranscodeSimpleTask(string $template)
    {
        $this->rsDao->save(RC::ALI_VOD_TRANSCODE, $template);
    }

    public function updateSchedule(int $time): void
    {
        $this->rsDao->save(RC::SYSTEM_SCHEDULE, $time);
    }
}

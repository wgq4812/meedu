<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface RuntimeStatusServiceInterface
{
    public function setTencentVodDomainKey(bool $isOk);

    public function setTencentVodCdnKey(bool $isOk);

    public function setTencentVodDomain(string $domain);

    public function setTencentVodApp(string $appId);

    public function setTencentVodSecret(bool $isOk);

    public function setTencentVodEvent(string $url);

    public function setTencentVodTranscodeSimpleTask(bool $isOk);

    public function chunks(array $names): array;

    public function tencentVodStatus(): array;

    public function aliVodStatus(): array;

    public function setAliVodSecret(bool $isOk);

    public function setAliVodDomain(string $domain);

    public function setAliVodEvent(string $url);

    public function setAliVodTranscodeSimpleTask(string $template);
}

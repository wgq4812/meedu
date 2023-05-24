<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface TencentVodServiceInterface
{
    public function domainKeySet(string $domain, string $key);

    public function defaultDomainKeySet(string $key);

    public function domains(): array;

    public function apps(): array;

    public function storeApp(string $name);

    public function eventSet(string $subAppId, string $url);

    public function isTranscodeSimpleTaskExists(string $subAppId): bool;

    public function transcodeSimpleTaskSet(string $subAppId);

    public function deleteVideo(array $fileIds);

    public function transcodeSubmit(string $fileId, string $templateName): void;
}

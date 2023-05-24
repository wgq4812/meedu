<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface AliVodServiceInterface
{
    public function saveEventConfig(string $callbackKey, string $callbackUrl, string $appId);

    public function transcodeTemplateStore(string $appId);

    public function isTranscodeSimpleTaskExists(string $appId): bool;

    public function domains(int $page = 1, int $size = 50): array;

    public function transcodeSubmit(string $appid, string $fileId, string $templateName): void;

    public function transcodeDestroy(string $videoId): void;

    public function transcodeTemplates(string $appId): array;
}

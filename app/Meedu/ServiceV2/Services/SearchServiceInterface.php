<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface SearchServiceInterface
{

    public function search(string $keywords, int $page, int $size, string $type): array;

    public function updateOrCreate(string $resourceType, int $resourceId, array $data): void;

    public function destroy(string $resourceType, int $resourceId): void;

}

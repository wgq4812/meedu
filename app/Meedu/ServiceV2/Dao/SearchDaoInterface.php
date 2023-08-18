<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

interface SearchDaoInterface
{
    public function search(string $keywords, string $type): array;

    public function find(string $resourceType, int $resourceId): array;

    public function store(string $resourceType, int $resourceId, array $data): array;

    public function destroy(string $resourceType, int $resourceId): void;

    public function update(string $resourceType, int $resourceId, array $data): void;
}

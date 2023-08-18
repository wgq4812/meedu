<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

use App\Meedu\ServiceV2\Models\SearchRecord;

class SearchDao implements SearchDaoInterface
{
    public function search(string $keywords, string $type): array
    {
        return SearchRecord::search($keywords)->take($type ? 300 : 100)->get()->toArray();
    }

    public function store(string $resourceType, int $resourceId, array $data): array
    {
        $data = array_merge($data, [
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
        ]);
        $record = SearchRecord::create($data);
        return $record->toArray();
    }

    public function update(string $resourceType, int $resourceId, array $data): void
    {
        SearchRecord::query()->where('resource_id', $resourceId)->where('resource_type', $resourceType)->update($data);
    }


    public function find(string $resourceType, int $resourceId): array
    {
        $data = SearchRecord::query()->where('resource_id', $resourceId)->where('resource_type', $resourceType)->first();
        return $data ? $data->toArray() : [];
    }

    public function destroy(string $resourceType, int $resourceId): void
    {
        SearchRecord::query()->where('resource_id', $resourceId)->where('resource_type', $resourceType)->delete();
    }


}

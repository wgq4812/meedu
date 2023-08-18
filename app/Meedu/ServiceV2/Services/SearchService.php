<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Meedu\ServiceV2\Dao\SearchDaoInterface;

class SearchService implements SearchServiceInterface
{

    private $dao;

    public function __construct(SearchDaoInterface $dao)
    {
        $this->dao = $dao;
    }

    public function search(string $keywords, int $page, int $size, string $type): array
    {
        $data = $this->dao->search($keywords, $type);

        //如果存在type过滤[meilisearch-v0.21.0暂不支持增加type的过滤,这里需要先读取出数据然后手动过滤]
        if ($type) {
            $data = collect($data)->filter(function ($item) use ($type) {
                return $item['resource_type'] === $type;
            })->toArray();
        }

        $total = count($data);
        $chunks = array_chunk($data, $size);

        return [
            'total' => $total,
            'data' => $chunks[$page - 1] ?? [],
        ];
    }

    public function updateOrCreate(string $resourceType, int $resourceId, array $data): void
    {
        if ($this->dao->find($resourceType, $resourceId)) {
            $this->dao->update($resourceType, $resourceId, $data);
            return;
        }
        $this->dao->store($resourceType, $resourceId, $data);
    }

    public function destroy(string $resourceType, int $resourceId): void
    {
        $this->dao->destroy($resourceType, $resourceId);
    }

}

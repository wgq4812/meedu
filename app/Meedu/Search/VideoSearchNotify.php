<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Search;

use App\Meedu\ServiceV2\Services\ConfigServiceInterface;
use App\Meedu\ServiceV2\Services\SearchServiceInterface;

class VideoSearchNotify implements SearchNotifyContract
{
    public const RESOURCE_TYPE = 'video';

    public function closed()
    {
        /**
         * @var ConfigServiceInterface $configService
         */
        $configService = app()->make(ConfigServiceInterface::class);
        return $configService->enabledFullSearch() === false;
    }

    public function create(int $resourceId, array $data)
    {
        if ($this->closed()) {
            return;
        }

        /**
         * @var SearchServiceInterface $service
         */
        $service = app()->make(SearchServiceInterface::class);
        $service->updateOrCreate(self::RESOURCE_TYPE, $resourceId, $data);
    }

    public function update(int $resourceId, array $data)
    {
        if ($this->closed()) {
            return;
        }

        /**
         * @var SearchServiceInterface $service
         */
        $service = app()->make(SearchServiceInterface::class);
        $service->updateOrCreate(self::RESOURCE_TYPE, $resourceId, $data);
    }

    public function delete(int $resourceId)
    {
        if ($this->closed()) {
            return;
        }

        /**
         * @var SearchServiceInterface $service
         */
        $service = app()->make(SearchServiceInterface::class);
        $service->destroy(self::RESOURCE_TYPE, $resourceId);
    }

    public function deleteBatch(array $ids)
    {
        if ($this->closed()) {
            return;
        }

        /**
         * @var SearchServiceInterface $service
         */
        $service = app()->make(SearchServiceInterface::class);

        foreach ($ids as $id) {
            $service->destroy(self::RESOURCE_TYPE, $id);
        }
    }
}

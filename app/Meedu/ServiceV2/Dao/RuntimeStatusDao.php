<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

use App\Meedu\ServiceV2\Models\RuntimeStatus;

class RuntimeStatusDao implements RuntimeStatusDaoInterface
{
    public function save(string $name, $status)
    {
        RuntimeStatus::query()->where('name', $name)->update(['status' => $status]);
    }

    public function nameChunks(array $names): array
    {
        return RuntimeStatus::query()->whereIn('name', $names)->get()->toArray();
    }

    public function value(string $name)
    {
        return RuntimeStatus::query()->where('name', $name)->value('status');
    }
}

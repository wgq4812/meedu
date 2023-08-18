<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

use App\Meedu\ServiceV2\Models\AppConfig;

class ConfigDao implements ConfigDaoInterface
{
    public function updateByKey(string $key, string $value): void
    {
        AppConfig::query()->where('key', $key)->update(['value' => $value]);
    }

    public function all(): array
    {
        return AppConfig::query()->orderBy('sort')->get()->toArray();
    }


}

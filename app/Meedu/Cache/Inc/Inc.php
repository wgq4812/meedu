<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache\Inc;

use Illuminate\Support\Facades\Cache;

class Inc
{
    public static function record(IncItem $incItem): void
    {
        $key = $incItem->getKey();
        $times = (int)Cache::get($key, 0);
        if ($times + 1 >= $incItem->getLimit()) {
            Cache::forget($key);
            $incItem->save();
        } else {
            Cache::increment($key);
        }
    }
}

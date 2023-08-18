<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache\Impl;

use Illuminate\Support\Facades\Cache;

class VideoViewCache
{

    public const KEY = 'video-view:%d';

    public function inc(int $id)
    {
        Cache::increment($this->key($id));
    }

    public function get(int $id)
    {
        return Cache::get($this->key($id), 0);
    }

    private function key(int $id)
    {
        return sprintf(self::KEY, $id);
    }

}

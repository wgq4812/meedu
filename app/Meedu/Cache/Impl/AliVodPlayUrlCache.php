<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache\Impl;

use Illuminate\Support\Facades\Cache;

class AliVodPlayUrlCache
{

    public const KEY = 'ali-vod-url:%d-%d-%s';
    public const EXPIRE = 600;

    private $key;

    public function __construct(int $videoId, int $isTry, string $fileId)
    {
        $this->key = sprintf(self::KEY, $videoId, $isTry, $fileId);
    }

    public function put(string $data)
    {
        Cache::put($this->key, $data, self::EXPIRE);
    }

    public function get()
    {
        return Cache::get($this->key);
    }

}

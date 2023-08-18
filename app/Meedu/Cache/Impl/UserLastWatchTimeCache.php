<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache\Impl;

use Illuminate\Support\Facades\Cache;

class UserLastWatchTimeCache
{

    public const KEY = 'user-v-w-d:%d';
    public const EXPIRE = 259200;

    private $key;

    public function __construct(int $userId)
    {
        $this->key = sprintf(self::KEY, $userId);
    }


    public function get()
    {
        return Cache::get($this->key, 0);
    }

    public function put(int $timestamp): void
    {
        Cache::put($this->key, $timestamp, self::EXPIRE);
    }

}

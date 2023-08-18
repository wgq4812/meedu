<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache\Impl;

use Illuminate\Support\Facades\Cache;

class WechatScanCodeCache
{

    public const KEY = 'wechat-scan:%s';
    public const EXPIRE = 3600;

    private $key;

    public function __construct(string $code)
    {
        $this->key = sprintf(self::KEY, $code);
    }

    public function put(int $userId)
    {
        Cache::put($this->key, $userId, self::EXPIRE);
    }

    public function get()
    {
        return Cache::get($this->key, 0);
    }

    public function delete()
    {
        Cache::forget($this->key);
    }

}

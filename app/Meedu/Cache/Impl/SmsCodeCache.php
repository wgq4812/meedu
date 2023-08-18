<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache\Impl;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class SmsCodeCache
{

    public const KEY = 'sms-code:%s';
    public const EXPIRE = 300;

    public function put(string $mobile, string $code)
    {
        Cache::put($this->key($mobile), $code, self::EXPIRE);
    }

    public function get(string $mobile)
    {
        return Cache::get($this->key($mobile));
    }

    public function beyondLimit(string $mobile)
    {
        return self::EXPIRE - $this->ttl($mobile) >= 120;
    }

    public function ttl(string $mobile)
    {
        $prefix = Cache::getStore()->getPrefix();
        return Redis::connection()->client()->ttl($prefix . $this->key($mobile));
    }

    public function delete(string $mobile)
    {
        Cache::forget($this->key($mobile));
    }

    private function key(string $mobile)
    {
        return sprintf(self::KEY, $mobile);
    }

}

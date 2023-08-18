<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache\Impl;

use Illuminate\Support\Facades\Cache;

class SmsCodeTryCache
{

    public const KEY = 'sms-code-try:%s';
    public const EXPIRE = 300;

    public function inc(string $mobile)
    {
        Cache::increment($this->key($mobile));
    }

    public function get(string $mobile)
    {
        return (int)Cache::get($this->key($mobile), 0);
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

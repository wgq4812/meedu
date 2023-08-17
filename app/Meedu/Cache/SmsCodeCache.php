<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache;

use Illuminate\Support\Facades\Cache;

class SmsCodeCache
{

    public const KEY = 'sms-code:%s';
    public const EXPIRE = 300;

    public function put(string $mobile, string $code)
    {
        $key = sprintf(self::KEY, $mobile);
        Cache::put($key, $code, self::EXPIRE);
    }

}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache;

use Illuminate\Support\Facades\Cache;
use App\Meedu\ServiceV2\Services\OtherServiceInterface;

class NavCache
{
    public const KEY = 'system-pc-nav';
    public const EXPIRE = 259200;

    private $otherService;

    public function __construct(OtherServiceInterface $otherService)
    {
        $this->otherService = $otherService;
    }

    public function get()
    {
        $data = Cache::get(self::KEY);
        if (!$data) {
            $data = $this->otherService->navs();
            Cache::put(self::KEY, $data, self::EXPIRE);
        }
        return $data;
    }

    public function forgot()
    {
        Cache::forget(self::KEY);
    }
}

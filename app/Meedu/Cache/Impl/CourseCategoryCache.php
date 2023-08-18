<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache\Impl;

use Illuminate\Support\Facades\Cache;
use App\Meedu\ServiceV2\Services\CourseServiceInterface;

class CourseCategoryCache
{
    public const KEY = 'vod-categories';
    public const EXPIRE = 259200;

    private $courseService;

    public function __construct(CourseServiceInterface $courseService)
    {
        $this->courseService = $courseService;
    }

    public function get()
    {
        $data = Cache::get(self::KEY);
        if (!$data) {
            $data = $this->courseService->categories();
            Cache::put(self::KEY, $data, self::EXPIRE);
        }
        return $data;
    }

    public function forgot()
    {
        Cache::forget(self::KEY);
    }
}

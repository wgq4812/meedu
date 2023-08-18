<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\CourseCategoryUpdateEvent;

use App\Events\CourseCategoryUpdateEvent;
use App\Meedu\Cache\Impl\CourseCategoryCache;

class CacheClearListener
{
    private $categoryCache;

    public function __construct(CourseCategoryCache $categoryCache)
    {
        $this->categoryCache = $categoryCache;
    }

    public function handle(CourseCategoryUpdateEvent $event)
    {
        $this->categoryCache->forgot();
    }
}

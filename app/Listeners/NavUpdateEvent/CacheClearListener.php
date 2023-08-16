<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\NavUpdateEvent;

use App\Meedu\Cache\NavCache;
use App\Events\NavUpdateEvent;

class CacheClearListener
{
    private $navCache;

    public function __construct(NavCache $navCache)
    {
        $this->navCache = $navCache;
    }

    public function handle(NavUpdateEvent $event)
    {
        $this->navCache->forgot();
    }
}

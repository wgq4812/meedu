<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\LinkUpdateEvent;

use App\Meedu\Cache\LinkCache;
use App\Events\LinkUpdateEvent;

class CacheClearListener
{
    private $linkCache;

    public function __construct(LinkCache $linkCache)
    {
        $this->linkCache = $linkCache;
    }

    public function handle(LinkUpdateEvent $event)
    {
        $this->linkCache->forgot();
    }
}

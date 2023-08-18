<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\ViewBlockUpdateEvent;

use App\Events\ViewBlockUpdateEvent;
use App\Meedu\Cache\Impl\ViewBlockH5IndexPageCache;
use App\Meedu\Cache\Impl\ViewBlockPCIndexPageCache;

class CacheClearListener
{
    private $h5IndexPageCache;

    private $PCIndexPageCache;

    public function __construct(ViewBlockH5IndexPageCache $h5IndexPageCache, ViewBlockPCIndexPageCache $PCIndexPageCache)
    {
        $this->h5IndexPageCache = $h5IndexPageCache;
        $this->PCIndexPageCache = $PCIndexPageCache;
    }

    public function handle(ViewBlockUpdateEvent $event)
    {
        if ($event->page === 'pc-page-index' && $event->platform === 'pc') {
            $this->PCIndexPageCache->forgot();
        } elseif ($event->page === 'h5-page-index' && $event->platform === 'h5') {
            $this->h5IndexPageCache->forgot();
        }
    }
}

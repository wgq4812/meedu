<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class TencentVodCallbackEvent
{
    use Dispatchable, SerializesModels;

    public $event;
    public $params;

    public function __construct(string $event, array $params)
    {
        $this->event = $event;
        $this->params = $params;
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class AliVodCallbackEvent
{
    use Dispatchable, SerializesModels;

    public $timestamp;
    public $event;
    public $params;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $event, int $timestamp, array $params)
    {
        $this->event = $event;
        $this->timestamp = $timestamp;
        $this->params = $params;
    }
}

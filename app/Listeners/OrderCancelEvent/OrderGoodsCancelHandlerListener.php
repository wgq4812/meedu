<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\OrderCancelEvent;

use App\Events\OrderCancelEvent;
use App\Meedu\Bus\Order\OrderHandler;

class OrderGoodsCancelHandlerListener
{
    private $orderHandler;

    public function __construct(OrderHandler $orderHandler)
    {
        $this->orderHandler = $orderHandler;
    }

    public function handle(OrderCancelEvent $event)
    {
        $this->orderHandler->cancel($event->orderId);
    }
}

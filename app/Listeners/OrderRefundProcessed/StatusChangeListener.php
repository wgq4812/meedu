<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\OrderRefundProcessed;

use App\Events\OrderRefundProcessed;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;

class StatusChangeListener
{
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    public function handle(OrderRefundProcessed $event)
    {
        $this->orderService->changeOrderRefundStatus($event->orderRefund['id'], $event->status);
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\PaymentSuccessEvent;

use App\Events\PaymentSuccessEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;

class StatusChangeListener implements ShouldQueue
{
    use InteractsWithQueue;

    private $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }


    /**
     * @param $event PaymentSuccessEvent
     */
    public function handle($event)
    {
        $this->orderService->change2Paid($event->order['id']);
    }
}

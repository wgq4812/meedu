<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\PaymentSuccessEvent;

use App\Meedu\Bus\Order\OrderHandler;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeliverListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $orderHandler;

    public function __construct(OrderHandler $orderHandler)
    {
        $this->orderHandler = $orderHandler;
    }

    public function handle($event)
    {
        $this->orderHandler->delivery($event->order['id']);
    }
}

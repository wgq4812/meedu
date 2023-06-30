<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\OrderRefundProcessed;

use App\Bus\RefundBus;
use App\Events\OrderRefundProcessed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Member\Services\NotificationService;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;
use App\Services\Member\Interfaces\NotificationServiceInterface;

class UserNotifyListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var NotificationService
     */
    protected $notificationService;

    protected $orderService;

    protected $refundBus;

    public function __construct(
        NotificationServiceInterface $notificationService,
        RefundBus                    $refundBus,
        OrderServiceInterface        $orderService
    ) {
        $this->notificationService = $notificationService;
        $this->refundBus = $refundBus;
        $this->orderService = $orderService;
    }

    public function handle(OrderRefundProcessed $event)
    {
        if (!$this->refundBus->isSuccess($event->status)) {
            return;
        }

        $order = $this->orderService->findById($event->orderRefund['order_id']);

        $this->notificationService->notify(
            $event->orderRefund['user_id'],
            __('订单:orderNo已成功退款:amount元', [
                'orderNo' => $order['order_id'],
                'amount' => $event->orderRefund['amount'] / 100,
            ])
        );
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\Wechat;

use App\Bus\RefundBus;
use Yansongda\Pay\Gateways\Wechat;
use Illuminate\Support\Facades\Log;
use App\Events\OrderRefundProcessed;
use App\Meedu\Payment\Wechat\WechatRefund;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;

class RefundController
{
    public function notify(WechatRefund $wechatRefund, RefundBus $refundBus, OrderServiceInterface $orderService)
    {
        return $wechatRefund->callback(function ($wechat) use ($refundBus, $orderService) {
            /**
             * @var Wechat $wechat
             */

            $result = $wechat->verify(null, true)->toArray();

            Log::info('微信退款回调数据', $result);

            $refundNo = $result['out_refund_no'];

            $refundOrder = $orderService->findOrderRefund($refundNo);
            if ($refundBus->isProcessed($refundOrder)) {
                return $wechat->success();
            }

            $status = $refundBus->wechatRefundStatusMap($result['refund_status']);

            event(new OrderRefundProcessed($refundOrder, $status, []));
        });
    }
}

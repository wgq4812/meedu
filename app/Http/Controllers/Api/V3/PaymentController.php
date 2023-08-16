<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use App\Constant\FrontendConstant;
use App\Http\Controllers\Api\BaseController;
use App\Meedu\Payment\PaymentHandler;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;
use Illuminate\Http\Request;

class PaymentController extends BaseController
{
    /**
     * @api {post} /api/v3/order/pay 订单支付
     * @apiGroup 订单-V3
     * @apiName V3OrderPay
     * @apiVersion v3.0.0
     * @apiHeader Authorization Bearer+空格+token
     *
     * @apiParam {String} order_id 订单编号
     * @apiParam {String=alipay,alipay-h5,wechat,wechat-jsapi,handPay-pc,handPay-h5,handPay-wechat-mini,handPay-app} payment 支付网关
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     */
    public function submit(Request $request, PaymentHandler $paymentHandler, OrderServiceInterface $orderService)
    {
        $orderId = $request->input('order_id');
        $payment = $request->input('payment');
        if (!$orderId || !$payment) {
            return $this->error(__('参数错误'));
        }
        $order = $orderService->findUserOrder($this->id(), $orderId);

        if ($order['payment']) {
            return $this->error(__('当前订单已选择支付方式'));
        }
        if (FrontendConstant::ORDER_UN_PAY !== $order['status']) {
            return $this->error(__('订单状态错误'));
        }

        $paymentStatus = $paymentHandler->setPayment($payment)->create($order);

        return $paymentStatus->data;
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Payment;

use App\Meedu\Cache\MemoryCache;
use App\Businesses\BusinessState;
use App\Events\PaymentSuccessEvent;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceException;
use App\Meedu\Payment\Contract\Payment;
use App\Meedu\Payment\Contract\PaymentStatus;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class PaymentHandler
{
    private $payments;

    private $businessState;

    private $orderService;

    public function __construct(ConfigServiceInterface $configService, BusinessState $businessState, OrderServiceInterface $orderService)
    {
        $this->payments = $configService->payments();
        $this->businessState = $businessState;
        $this->orderService = $orderService;
    }

    private $payment;

    /**
     * @return string
     */
    public function getPayment(): string
    {
        return $this->payment;
    }

    /**
     * @param string $payment
     * @return $this
     * @throws ServiceException
     */
    public function setPayment(string $payment)
    {
        if (!isset($this->payments[$payment])) {
            throw new ServiceException(__('支付网关不存在'));
        }
        if ((int)$this->payments[$payment]['enabled'] !== 0) {
            throw new ServiceException(__('支付网关未启用'));
        }
        $this->setPayment($payment);
        return $this;
    }

    /**
     * @return Payment
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function getPaymentHandler(): Payment
    {
        return app()->make($this->payments[$this->payment]['handler']);
    }

    public function create(array $order, array $extra): PaymentStatus
    {
        $total = $this->businessState->calculateOrderNeedPaidSum($order);
        if ($total === 0) {
            throw new ServiceException(__('无需继续支付'));
        }

        $updateData = [
            'payment' => $this->payment,
            'payment_method' => $this->payments[$this->payment]['method'],
        ];
        $orderUpdateRes = $this->orderService->change2Paying($order['user_id'], $order['id'], $order['status'], $updateData);
        if (!$orderUpdateRes) {
            Log::error(__METHOD__ . '|订单状态修改失败|信息:' . json_encode(array_merge($updateData, ['id' => $order['id']])));
            throw new ServiceException(__('订单状态修改失败'));
        }

        return $this->getPaymentHandler()->create($order['order_id'], $order['order_id'], $total, $extra);
    }

    public function callback()
    {
        $handler = $this->getPaymentHandler();
        $orderNo = $handler->callback();
        if ($orderNo === false) {
            abort(406);
        }

        $order = $this->orderService->find($orderNo);

        MemoryCache::getInstance()->set($orderNo, $order, true);

        event(new PaymentSuccessEvent($order));
    }
}

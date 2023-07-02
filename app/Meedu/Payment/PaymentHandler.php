<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Payment;

use App\Meedu\Cache\MemoryCache;
use App\Events\PaymentSuccessEvent;
use App\Exceptions\ServiceException;
use App\Meedu\Payment\Contract\Payment;
use App\Meedu\Payment\Contract\PaymentStatus;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class PaymentHandler
{
    private $payments;

    private $payment;

    private $orderService;

    public function __construct(ConfigServiceInterface $configService, OrderServiceInterface $orderService)
    {
        $this->payments = $configService->payments();
        $this->orderService = $orderService;
    }

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
        if ((int)$this->payments[$payment]['enabled'] !== 1) {
            throw new ServiceException(__('支付网关未启用'));
        }
        $this->payment = $payment;
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

    public function create(array $order, array $extra = []): PaymentStatus
    {
        $total = $this->orderService->continuePayAmount($order['id']);
        if ($total <= 0) {
            throw new ServiceException(__('无需继续支付'));
        }

        $updateData = [
            'payment' => $this->payment,
            'payment_method' => $this->payment,
        ];
        $this->orderService->change2Paying($order['user_id'], $order['id'], $updateData);

        $title = $this->orderService->getOrderGoodsTitle($order['id']);

        return $this->getPaymentHandler()->create($order['order_id'], $title, $total, $extra);
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

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Bus\Order;

use App\Exceptions\ServiceException;
use App\Meedu\Bus\Order\Core\OrderInterface;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class OrderHandler
{
    private $type;

    private $orderService;

    private $registerOrderHandler;

    /**
     * @var OrderInterface
     */
    private $handler;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        if (!isset($this->registerOrderHandler[$type])) {
            throw new ServiceException(__('订单处理器不存在'));
        }
        if ((int)$this->registerOrderHandler[$type]['enabled'] !== 1) {
            throw new ServiceException(__('订单处理器已关闭'));
        }
        $this->type = $type;
        $this->handler = app()->make($this->registerOrderHandler[$this->getType()]['handler']);
        return $this;
    }

    public function __construct(OrderServiceInterface $orderService, ConfigServiceInterface $configService)
    {
        $this->orderService = $orderService;
        $this->registerOrderHandler = $configService->getOrderHandler();
    }

    public function create(int $userId, int $id, string $promoCode): array
    {
        $this->handler->check($userId, [$id]);

        $goodsList = $this->handler->goodsList([$id]);

        $total = array_reduce($goodsList, function ($prev, $item) {
            return ($prev ?? 0) + $item['charge'];
        });
        if ($total === 0) {
            throw new ServiceException(__('订单总价不能为0'));
        }

        $order = $this->orderService->storeOrder($userId, $total, $goodsList, $promoCode);
        $order['continue_pay_amount'] = $this->orderService->continuePayAmount($order['id']);
        return $order;
    }

    public function cancel(int $orderId): void
    {
        $orderGoods = $this->orderService->orderGoodsList($orderId);
        foreach ($orderGoods as $goodsItem) {
            $goodsType = $goodsItem['goods_type'];
            if (!isset($this->registerOrderHandler[$goodsType])) {
                continue;
            }
            /**
             * @var OrderInterface $handler
             */
            $handler = app()->make($this->registerOrderHandler[$goodsType]['handler']);
            $handler->cancel($goodsItem);
        }
    }

    public function refund(): void
    {
        // 退款处理
        // 比如：收回课程
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceException;
use App\Meedu\ServiceV2\Dao\OrderDaoInterface;

class OrderService implements OrderServiceInterface
{
    private $orderDao;

    public function __construct(OrderDaoInterface $orderDao)
    {
        $this->orderDao = $orderDao;
    }

    public function find(string $orderNo)
    {
        return $this->orderDao->find(['order_id' => $orderNo]);
    }

    public function findUserOrder(int $userId, string $orderNo)
    {
        return $this->orderDao->find(['user_id' => $userId, 'order_id' => $orderNo]);
    }

    public function change2Paying(int $userId, int $orderId, int $status, array $data)
    {
        $result = $this->orderDao->update($data, [
            'user_id' => $userId,
            'id' => $orderId,
            'status' => $status,
        ]);
        if (!$result) {
            Log::error(__METHOD__ . '|订单状态写入失败', compact('userId', 'orderId', 'status', 'data'));
            throw new ServiceException('订单状态写入失败');
        }
    }

    public function getOrderGoodsTitle(string $id): string
    {
        $goods = $this->orderDao->orderGoods($id);
        return implode('|', array_compress($goods, 'goods_name'));
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Events\OrderCancelEvent;
use App\Constant\FrontendConstant;
use Illuminate\Support\Facades\DB;
use App\Events\PaymentSuccessEvent;
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

    public function find(string $orderNo): array
    {
        return $this->orderDao->find(['order_id' => $orderNo]);
    }

    public function findById(int $id): array
    {
        return $this->orderDao->find(['id' => $id]);
    }

    public function orderStatus(int $userId, string $orderNo): int
    {
        $order = $this->orderDao->find([
            'order_id' => $orderNo,
            'user_id' => $userId,
        ]);
        return $order['status'];
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

    public function orderGoodsList(int $id): array
    {
        return $this->orderDao->orderGoods($id);
    }

    public function promoCode(string $code): array
    {
        return $this->orderDao->promoCode($code);
    }

    public function canUsePromoCode(int $userId, array $promoCode): bool
    {
        if ($userId === $promoCode['user_id']) {
            //兼容老数据-user_id>0属于学员的邀请码
            return false;
        }
        if ($promoCode['use_times'] !== 0 && $promoCode['use_times'] <= $promoCode['used_times']) {
            //限制使用次数 & 已达到最大使用次数
            return false;
        }
        // 本方法是 meedu-v5 版本开始的检测优惠码是否使用的方法
        // 通过引入学员优惠码使用记录表记录
        if ($this->orderDao->userPromoCodePaidRecord($userId, $promoCode['id'])) {
            return false;
        }
        // 下面是 meedu-v5 版本之前的优惠码是否使用过的检测方法
        // 通过查询学员的全部订单支付记录里面是否存在此优惠码的记录
        return !$this->orderDao->userPromoCodePaidRecord($userId, $promoCode['id']);
    }

    public function storeOrder(int $userId, int $total, array $goodsList, string $promoCode): array
    {
        return DB::transaction(function () use ($userId, $total, $goodsList, $promoCode) {
            $discount = 0;
            $promoCodeInfo = [];
            $canUsePromoCode = false;

            if ($promoCode) {
                $promoCodeInfo = $this->orderDao->promoCode($promoCode);
                if ($promoCodeInfo && $canUsePromoCode = $this->canUsePromoCode($userId, $promoCodeInfo)) {
                    $discount = min($promoCodeInfo['invite_user_reward'], $total);
                    // 优惠码使用次数+1
                    $this->orderDao->promoCodUsedTimeInc($promoCodeInfo['id'], $promoCodeInfo['used_times']);
                }
            }

            // 实际学员需要支付金
            $payAmount = $total - $discount;
            // 订单状态
            $orderStatus = $payAmount === 0 ? FrontendConstant::ORDER_PAID : FrontendConstant::ORDER_UN_PAY;

            // 创建订单
            $order = $this->orderDao->storeOrder($userId, $total, $orderStatus);
            // 创建订单商品
            $this->orderDao->storeOrderGoods($order, $goodsList);
            // 记录优化的使用记录
            if ($canUsePromoCode) {
                $this->orderDao->storeOrderPaidPromoCode($order, $discount, $promoCodeInfo);
                $this->orderDao->storeUserPromoCodeRecord($userId, $promoCodeInfo['id'], $order['id'], $promoCodeInfo['invite_user_reward'], $discount);
            }

            if ($orderStatus === FrontendConstant::ORDER_PAID) {
                event(new PaymentSuccessEvent($order));
            }

            return $order;
        });
    }

    public function cancelOrder(array $order): void
    {
        if ($order['status'] !== FrontendConstant::ORDER_PAYING) {
            throw new ServiceException(__('订单状态错误'));
        }

        DB::transaction(function () use ($order) {
            // 修改订单状态为『已取消』
            $result = $this->orderDao->update(['status' => FrontendConstant::ORDER_CANCELED], [
                'id' => $order['id'],
                'status' => $order['status'],
            ]);
            if (!$result) {
                Log::error(__METHOD__ . '|订单取消失败|信息:' . json_encode(['id' => $order['id'], 'status' => $order['status']]));
                throw new \Exception(__('系统错误'));
            }

            // 如果使用了promoCode的话则取消
            $paidRecords = $this->orderDao->paidRecords($order['id']);
            if ($paidRecords) {
                foreach ($paidRecords as $paidRecordItem) {
                    if (FrontendConstant::ORDER_PAID_TYPE_PROMO_CODE !== $paidRecordItem['paid_type']) {
                        continue;
                    }
                    // 删除学员使用优惠码的记录
                    $this->orderDao->destroyUserPromoCodeRecord($order['user_id'], $paidRecordItem['paid_type_id'], $order['id']);
                    // 恢复优惠码的已使用次数
                    $promoCode = $this->orderDao->promoCodeById($paidRecordItem['paid_type_id']);
                    if ($promoCode) {
                        $this->orderDao->promoCodUsedTimeInc($promoCode['id'], $promoCode['used_times'], -1);
                    }
                }
            }

            event(new OrderCancelEvent($order['id']));
        });
    }

    public function continuePayAmount(int $orderId): int
    {
        $order = $this->orderDao->find(['id' => $orderId]);
        $paidRecords = $this->orderDao->paidRecords($orderId);
        $amount = $order['charge'];
        foreach ($paidRecords as $paidRecordItems) {
            $amount -= $paidRecordItems['paid_total'];
        }
        return $amount;
    }
}

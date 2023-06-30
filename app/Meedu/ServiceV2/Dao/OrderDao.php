<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

use Carbon\Carbon;
use App\Constant\FrontendConstant;
use Illuminate\Support\Facades\Log;
use App\Meedu\ServiceV2\Models\Order;
use App\Meedu\ServiceV2\Models\PromoCode;
use App\Meedu\ServiceV2\Models\OrderGoods;
use App\Meedu\ServiceV2\Models\OrderPaidRecord;
use App\Meedu\ServiceV2\Models\UserPromoCodeRecord;

class OrderDao implements OrderDaoInterface
{
    public function find(array $params): array
    {
        return Order::query()
            ->when(isset($params['id']), function ($query) use ($params) {
                $query->where('id', $params['id']);
            })
            ->when(isset($params['order_id']), function ($query) use ($params) {
                $query->where('order_id', $params['order_id']);
            })
            ->when(isset($params['user_id']), function ($query) use ($params) {
                $query->where('user_id', $params['user_id']);
            })
            ->firstOrFail()
            ->toArray();
    }

    public function update(array $data, array $params): int
    {
        return Order::query()
            ->when(isset($params['id']), function ($query) use ($params) {
                $query->where('id', $params['id']);
            })
            ->when(isset($params['order_id']), function ($query) use ($params) {
                $query->where('order_id', $params['order_id']);
            })
            ->when(isset($params['user_id']), function ($query) use ($params) {
                $query->where('user_id', $params['user_id']);
            })
            ->when(isset($params['status']), function ($query) use ($params) {
                $query->where('status', $params['status']);
            })
            ->update($data);
    }

    public function orderGoods(int $orderId): array
    {
        return OrderGoods::query()->where('oid', $orderId)->get()->toArray();
    }

    public function userPromoCodePaidRecord(int $userId, int $promoCodeId): array
    {
        $record = OrderPaidRecord::query()
            ->where('user_id', $userId)
            ->where('paid_type', FrontendConstant::ORDER_PAID_TYPE_PROMO_CODE)
            ->where('paid_type_id', $promoCodeId)
            ->first();
        return $record ? $record->toArray() : [];
    }

    public function userPromoCodeRecord(int $userId, int $codeId): array
    {
        $record = UserPromoCodeRecord::query()
            ->where('user_id', $userId)
            ->where('code_id', $codeId)
            ->first();
        return $record ? $record->toArray() : [];
    }


    public function promoCode(string $code): array
    {
        $code = PromoCode::query()->where('code', $code)->first();
        return $code ? $code->toArray() : [];
    }

    public function promoCodeById(int $id): array
    {
        $code = PromoCode::query()->where('id', $id)->first();
        return $code ? $code->toArray() : [];
    }

    public function promoCodUsedTimeInc(int $id, int $beforeUsedTimes, int $amount = 1): void
    {
        $result = PromoCode::query()->where('id', $id)->where('used_times', $beforeUsedTimes)->increment('used_times', $amount);
        if (!$result) {
            Log::error(__METHOD__ . '|优惠码使用次数inc失败|' . json_encode(compact('id', 'beforeUsedTimes')));
            throw new \Exception(__('系统错误'));
        }
    }

    public function storeOrder(int $userId, int $total, int $status): array
    {
        $data = Order::create([
            'user_id' => $userId,
            'charge' => $total,
            'status' => $status,
            'order_id' => $userId . date('Ymd') . mt_rand(10, 99),
        ]);
        return $data->toArray();
    }

    public function storeOrderGoods(array $order, array $goodsList): void
    {
        $orderGoodsItems = [];
        $now = Carbon::now()->toDateTimeLocalString();
        foreach ($goodsList as $goodsItem) {
            $orderGoodsItems[] = [
                'user_id' => $order['user_id'],
                'oid' => $order['id'],
                'num' => 1,
                'charge' => $goodsItem['charge'],
                'goods_id' => $goodsItem['goods_id'],
                'goods_type' => $goodsItem['goods_type'],
                'goods_name' => $goodsItem['goods_name'] ?? '',
                'goods_thumb' => $goodsItem['goods_thumb'] ?? '',
                'goods_charge' => (int)($goodsItem['goods_charge'] ?? ''),
                'goods_ori_charge' => (int)($goodsItem['goods_ori_charge'] ?? ''),
                'created_at' => $now,
                'updated_at' => $now,

                // todo 即将废弃
                'order_id' => '',
            ];
        }
        OrderGoods::insert($orderGoodsItems);
    }

    public function storeOrderPaidPromoCode(array $order, int $discount, array $promoCodeInfo): void
    {
        OrderPaidRecord::create([
            'user_id' => $order['user_id'],
            'order_id' => $order['id'],
            'paid_total' => $discount,
            'paid_type' => FrontendConstant::ORDER_PAID_TYPE_PROMO_CODE,
            'paid_type_id' => $promoCodeInfo['id']
        ]);
    }

    public function storeUserPromoCodeRecord(int $userId, int $codeId, int $orderId, int $originalAmount, int $discount): void
    {
        UserPromoCodeRecord::create([
            'user_id' => $userId,
            'code_id' => $codeId,
            'order_id' => $orderId,
            'original_amount' => $originalAmount,
            'discount' => $discount,
            'created_at' => Carbon::now()->toDateTimeLocalString(),
        ]);
    }

    public function destroyUserPromoCodeRecord(int $userId, int $codeId, int $orderId): void
    {
        UserPromoCodeRecord::query()
            ->where('user_id', $userId)
            ->where('code_id', $codeId)
            ->where('order_id', $orderId)
            ->delete();
    }

    public function paidRecords(int $orderId): array
    {
        return OrderPaidRecord::query()->where('order_id', $orderId)->get()->toArray();
    }
}

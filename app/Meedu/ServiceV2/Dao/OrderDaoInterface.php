<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

interface OrderDaoInterface
{
    public function find(array $params): array;

    public function update(array $data, array $params): int;

    public function orderGoods(int $orderId): array;

    public function userPromoCodeRecord(int $userId, int $codeId): array;

    public function userPromoCodePaidRecord(int $userId, int $promoCodeId): array;

    public function promoCode(string $code): array;

    public function promoCodeById(int $id): array;

    public function promoCodUsedTimeInc(int $id, int $beforeUsedTimes, int $amount = 1): void;

    public function storeOrder(int $userId, int $total): array;

    public function storeOrderGoods(array $order, array $goodsList): void;

    public function storeOrderPaidPromoCode(array $order, int $discount, array $promoCodeInfo): void;

    public function storeOrderPaidHand(array $order, int $amount): void;

    public function storeUserPromoCodeRecord(int $userId, int $codeId, int $orderId, int $originalAmount, int $discount): void;

    public function destroyUserPromoCodeRecord(int $userId, int $codeId, int $orderId): void;

    public function paidRecords(int $orderId): array;

    public function findOrderRefund(string $refundNo): array;

    public function getTimeoutOrders(string $datetime): array;

    public function takeProcessingRefundOrders(int $limit): array;

    public function changeOrderRefundStatus(int $refundOrderId, int $status): int;

    public function userOrdersPaginate(int $userId, int $page, int $size): array;
}

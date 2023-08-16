<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface OrderServiceInterface
{
    public function find(string $orderNo): array;

    public function findById(int $id): array;

    public function orderStatus(int $userId, string $orderNo): int;

    public function findUserOrder(int $userId, string $orderNo): array;

    public function change2Paying(int $userId, int $orderId, array $data): void;

    public function change2Paid(int $id): void;

    public function getOrderGoodsTitle(string $id): string;

    public function orderGoodsList(int $id): array;

    public function promoCode(string $code): array;

    public function canUsePromoCode(int $userId, array $promoCode): bool;

    public function storeOrder(int $userId, int $total, array $goodsList, string $promoCode): array;

    public function cancelOrder(array $order): void;

    public function continuePayAmount(int $orderId): int;

    public function remainingAmountHandPay(int $orderId): void;

    public function findOrderRefund(string $refundNo): array;

    public function getTimeoutOrders(string $datetime): array;

    public function takeProcessingRefundOrders(int $limit): array;

    public function changeOrderRefundStatus(int $refundOrderId, int $status): void;

    public function userOrdersPaginate(int $userId, int $page, int $size): array;
}

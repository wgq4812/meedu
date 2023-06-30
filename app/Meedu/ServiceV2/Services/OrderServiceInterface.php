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

    public function findUserOrder(int $userId, string $orderNo);

    public function change2Paying(int $userId, int $orderId, int $status, array $data);

    public function getOrderGoodsTitle(string $id): string;

    public function orderGoodsList(int $id): array;

    public function promoCode(string $code): array;

    public function canUsePromoCode(int $userId, array $promoCode): bool;

    public function storeOrder(int $userId, int $total, array $goodsList, string $promoCode): array;

    public function cancelOrder(array $order): void;

    public function continuePayAmount(int $orderId): int;
}

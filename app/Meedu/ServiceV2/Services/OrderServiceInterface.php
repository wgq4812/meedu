<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface OrderServiceInterface
{
    public function find(string $orderNo);

    public function findUserOrder(int $userId, string $orderNo);

    public function change2Paying(int $userId, int $orderId, int $status, array $data);

    public function getOrderGoodsTitle(string $id): string;
}

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
}

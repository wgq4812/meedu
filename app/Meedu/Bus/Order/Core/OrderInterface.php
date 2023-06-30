<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Bus\Order\Core;

interface OrderInterface
{
    public function check(int $userId, array $ids): void;

    public function goodsList(array $ids): array;

    public function cancel(array $orderGoods): void;

    public function refundConfirm(array $orderGoods): void;
}

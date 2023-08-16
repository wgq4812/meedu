<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Payment\Contract;

interface Payment
{
    public function create(string $orderNo, string $title, int $realAmount, array $extra = []): PaymentStatus;

    public function callback();
}

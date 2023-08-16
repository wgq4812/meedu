<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Payment\HandPay;

use App\Meedu\Payment\Contract\Payment;
use App\Meedu\Payment\Contract\PaymentStatus;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class HandPay implements Payment
{
    private $configService;

    public function __construct(ConfigServiceInterface $configService)
    {
        $this->configService = $configService;
    }

    public function create(string $orderNo, string $title, int $realAmount, array $extra = []): PaymentStatus
    {
        return new PaymentStatus(true, response()->json([
            'code' => 0,
            'message' => '',
            'data' => [
                'desc' => $this->configService->getHandPayDesc(),
            ],
        ]));
    }

    public function callback(): bool
    {
        return false;
    }
}

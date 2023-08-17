<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Meedu\Sms\SmsInterface;

class SmsService implements SmsServiceInterface
{
    public function sendCode(string $mobile, string $code, string $scene): void
    {
        /**
         * @var SmsInterface $handler
         */
        $handler = app()->make(SmsInterface::class);
        $handler->sendCode($mobile, $code, $scene);
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Sms;

interface SmsInterface
{
    public function sendCode(string $mobile, string $code, string $scene): void;
}

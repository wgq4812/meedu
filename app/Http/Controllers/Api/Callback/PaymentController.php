<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\Callback;

use App\Meedu\Payment\PaymentHandler;

class PaymentController
{
    public function callback(PaymentHandler $paymentHandler, $payment)
    {
        $paymentHandler->setPayment($payment)->callback();
        return 'success';
    }
}

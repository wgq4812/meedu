<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Payment\Alipay;

use Exception;
use Yansongda\Pay\Pay;
use Illuminate\Support\Facades\Log;
use App\Meedu\Payment\Contract\PaymentStatus;

class AlipayPayWeb extends AlipayPayBase
{
    public function create(string $orderNo, string $title, int $realAmount, array $extra = []): PaymentStatus
    {
        $payOrderData = [
            'out_trade_no' => $orderNo,
            'total_amount' => $realAmount,
            'subject' => $title,
        ];
        $payOrderData = array_merge($payOrderData, $extra);

        try {
            $result = Pay::alipay($this->getConfig())->web($payOrderData);

            return new PaymentStatus(true, response()->json([
                'code' => 0,
                'message' => '',
                'data' => [
                    'redirect_content' => $result->getContent(),
                ],
            ]));
        } catch (Exception $e) {
            Log::error(__METHOD__ . '|支付宝订单创建失败,信息:' . $e->getMessage(), compact('orderNo', 'title', 'realAmount'));
            return new PaymentStatus(false, response()->json([
                'code' => -1,
                'message' => __('支付宝订单创建失败'),
                'data' => [],
            ]));
        }
    }
}

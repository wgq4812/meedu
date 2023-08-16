<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Payment\Wechat;

use Yansongda\Pay\Pay;
use Illuminate\Support\Facades\Log;
use App\Meedu\Payment\Contract\PaymentStatus;

class WechatPayApp extends WechatPayBase
{
    public function create(string $orderNo, string $title, int $realAmount, array $extra = []): PaymentStatus
    {
        try {
            $payOrderData = [
                'out_trade_no' => $orderNo,
                'total_fee' => $realAmount * 100,
                'body' => $title,
            ];
            $payOrderData = array_merge($payOrderData, $extra);

            $createResult = Pay::wechat($this->getConfig())->app($payOrderData);

            return new PaymentStatus(true, response()->json([
                'code' => 0,
                'message' => '',
                'data' => json_decode($createResult->getContent(), true),
            ]));
        } catch (\Exception $exception) {
            Log::error(__METHOD__ . '|微信APP支付订单创建失败,信息:' . $exception->getMessage());
            return new PaymentStatus(false, response()->json([
                'code' => -1,
                'message' => __('微信订单创建失败'),
                'data' => [],
            ]));
        }
    }
}

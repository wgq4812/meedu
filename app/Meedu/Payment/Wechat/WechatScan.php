<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Payment\Wechat;

use Exception;
use Yansongda\Pay\Pay;
use Illuminate\Support\Facades\Log;
use App\Meedu\Payment\Contract\PaymentStatus;

class WechatScan extends WechatPayBase
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

            // 创建微信支付订单
            // $createResult['code_url'] = 二维码的内容
            $createResult = Pay::wechat($this->getConfig())->scan($payOrderData);

            return new PaymentStatus(true, response()->json([
                'code' => 0,
                'message' => '',
                'data' => [
                    'code_url' => $createResult['code_url'],
                ],
            ]));
        } catch (Exception $exception) {
            Log::error(__METHOD__ . '|微信扫码支付订单创建失败,信息:' . $exception->getMessage());
            return new PaymentStatus(false, response()->json([
                'code' => -1,
                'message' => __('微信订单创建失败'),
                'data' => [],
            ]));
        }
    }
}

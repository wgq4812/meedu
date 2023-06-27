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
use App\Meedu\Payment\Contract\Payment;
use App\Meedu\Payment\Contract\PaymentStatus;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class WechatScan implements Payment
{
    private $config;

    public function __construct(ConfigServiceInterface $configService)
    {
        $this->config = $configService->getWechatPayConfig();
    }

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
            $createResult = Pay::wechat($this->config)->scan($payOrderData);

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

    public function callback()
    {
        $pay = Pay::wechat($this->config);

        try {
            $data = $pay->verify();
            Log::info(__METHOD__ . '|微信支付回调数据|.' . json_encode($data));
            return $data['out_trade_no'];
        } catch (Exception $e) {
            exception_record($e);
            return false;
        }
    }
}

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
use Illuminate\Http\RedirectResponse;
use App\Meedu\Payment\Contract\Payment;
use App\Meedu\Payment\Contract\PaymentStatus;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class AlipayH5 implements Payment
{
    private $config;

    public function __construct(ConfigServiceInterface $configService)
    {
        $this->config = $configService->getAlipayConfig();
    }

    public function create(string $orderNo, string $title, int $realAmount, array $extra = []): PaymentStatus
    {
        $payOrderData = [
            'out_trade_no' => $orderNo,
            'total_amount' => $realAmount,
            'subject' => $title,
        ];
        $payOrderData = array_merge($payOrderData, $extra);

        try {
            /**
             * @var RedirectResponse $result
             */
            $result = Pay::alipay($this->config)->wap($payOrderData);

            return new PaymentStatus(true, response()->json([
                'code' => 0,
                'message' => '',
                'data' => [
                    'redirect_url' => $result->getTargetUrl(),
                ],
            ]));
        } catch (Exception $e) {
            Log::error(__METHOD__ . '|支付宝H5订单创建失败,信息:' . $e->getMessage(), compact('orderNo', 'title', 'realAmount'));
            return new PaymentStatus(false, response()->json([
                'code' => -1,
                'message' => __('支付宝订单创建失败'),
                'data' => [],
            ]));
        }
    }

    public function callback()
    {
        $pay = Pay::alipay($this->config);

        try {
            $data = $pay->verify();

            $notifyType = $data['notify_type'];
            $tradeStatus = $data['trade_status'];

            Log::info(__METHOD__ . '|支付宝支付回调数据|' . $notifyType . '|' . $tradeStatus . '|原始数据:' . json_encode($data));

            if (!($notifyType === 'trade_status_sync' && $tradeStatus === 'TRADE_SUCCESS')) {
                Log::info('非支付成功回调');
                return false;
            }

            return $data['out_trade_no'];
        } catch (Exception $e) {
            Log::error(__METHOD__ . '|支付宝回调错误,信息:' . $e->getMessage());
            return false;
        }
    }
}

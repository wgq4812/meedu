<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Payment\Alipay;

use Yansongda\Pay\Pay;
use Illuminate\Support\Facades\Log;
use App\Meedu\Payment\Contract\Payment;
use App\Meedu\Payment\Contract\PaymentStatus;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class AlipayPayBase implements Payment
{
    private $config;

    public function __construct(ConfigServiceInterface $configService)
    {
        $this->setConfig($configService->getAlipayConfig());
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function callback()
    {
        $pay = Pay::alipay($this->getConfig());

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
        } catch (\Exception $e) {
            Log::error(__METHOD__ . '|支付宝回调错误,信息:' . $e->getMessage());
            return false;
        }
    }

    public function create(string $orderNo, string $title, int $realAmount, array $extra = []): PaymentStatus
    {
        return new PaymentStatus(false);
    }
}

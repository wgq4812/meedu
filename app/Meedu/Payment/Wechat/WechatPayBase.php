<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Payment\Wechat;

use Yansongda\Pay\Pay;
use Illuminate\Support\Facades\Log;
use App\Meedu\Payment\Contract\Payment;
use App\Meedu\Payment\Contract\PaymentStatus;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class WechatPayBase implements Payment
{
    private $config;

    public function __construct(ConfigServiceInterface $configService)
    {
        $this->setConfig($configService->getWechatPayConfig());
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

    public function create(string $orderNo, string $title, int $realAmount, array $extra = []): PaymentStatus
    {
        return new PaymentStatus(false);
    }

    public function callback()
    {
        $pay = Pay::wechat($this->getConfig());

        try {
            $data = $pay->verify();
            Log::info(__METHOD__ . '|微信支付回调数据|.' . json_encode($data));
            return $data['out_trade_no'];
        } catch (\Exception $e) {
            exception_record($e);
            return false;
        }
    }
}

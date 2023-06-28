<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Payment\Wechat;

use Yansongda\Pay\Pay;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceException;
use App\Meedu\Payment\Contract\PaymentStatus;

class WechatPayMini extends WechatPayBase
{
    public function create(string $orderNo, string $title, int $realAmount, array $extra = []): PaymentStatus
    {
        // 微信小程序的用户openid
        $openid = $extra['openid'] ?? '';
        if (!$openid) {
            throw new ServiceException(__('未配置openid'));
        }

        try {
            $payOrderData = [
                'out_trade_no' => $orderNo,
                'total_fee' => $realAmount * 100,
                'body' => $title,
                'openid' => $openid,
            ];
            $payOrderData = array_merge($payOrderData, $extra);

            $createResult = Pay::wechat($this->getConfig())->miniapp($payOrderData);

            return new PaymentStatus(true, response()->json([
                'code' => 0,
                'message' => '',
                'data' => $createResult->toArray(),
            ]));
        } catch (\Exception $exception) {
            Log::error(__METHOD__ . '|微信小程序支付订单创建失败,信息:' . $exception->getMessage());
            return new PaymentStatus(false, response()->json([
                'code' => -1,
                'message' => __('微信订单创建失败'),
                'data' => [],
            ]));
        }
    }
}

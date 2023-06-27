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
use App\Exceptions\ServiceException;
use App\Meedu\Payment\Contract\Payment;
use App\Meedu\Payment\Contract\PaymentStatus;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class WechatJSAPI implements Payment
{
    private $config;

    public function __construct(ConfigServiceInterface $configService)
    {
        $this->config = $configService->getWechatPayConfig();
    }

    public function create(string $orderNo, string $title, int $realAmount, array $extra = []): PaymentStatus
    {
        if (!isset($extra['openid'])) {
            // 跳转到JSAPI支付页面
            // 下面将发起授权登录

            // 跳转的url
            $sUrl = strip_tags(request()->input('s_url', ''));
            $fUrl = strip_tags(request()->input('f_url', ''));
            if (!$sUrl || !$fUrl) {
                throw new ServiceException(__('未传递回调地址'));
            }
            // 构建Response
            $data = [
                'order_id' => $orderNo,
                's_url' => $sUrl,
                'f_url' => $fUrl,
                'expired_at' => time() + 3600,
            ];

            $payUrl = route('order.pay.wechat.jsapi', ['data' => encrypt($data)]);

            return new PaymentStatus(true, response()->json([
                'code' => 0,
                'message' => '',
                'data' => [
                    'redirect_url' => $payUrl,
                ],
            ]));
        }

        try {
            $payOrderData = [
                'out_trade_no' => $orderNo,
                'total_fee' => $realAmount * 100,
                'body' => $title,
                'openid' => $extra['openid'],
            ];

            $createResult = Pay::wechat($this->config)->mp($payOrderData);

            return new PaymentStatus(true, response()->json([
                'code' => 0,
                'message' => '',
                'data' => $createResult->toArray(),
            ]));
        } catch (Exception $exception) {
            Log::error(__METHOD__ . '|微信JSAPI支付订单创建失败,信息:' . $exception->getMessage());
            return new PaymentStatus(false, response()->json([
                'code' => -1,
                'message' => '',
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

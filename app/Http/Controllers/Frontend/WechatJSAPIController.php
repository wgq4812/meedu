<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Frontend;

use App\Meedu\Utils\Wechat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Meedu\Payment\PaymentHandler;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;

class WechatJSAPIController extends Controller
{
    public function index(Request $request, PaymentHandler $paymentHandler, OrderServiceInterface $orderService)
    {
        $data = $request->input('data');

        $openid = session('wechat_jsapi_openid');
        if (!$openid) {
            // 微信授权登录回调后
            if ($request->has('oauth')) {
                $user = Wechat::getInstance()->oauth->user();
                $openid = $user->getId();
                // 存储到session中
                session(['wechat_jsapi_openid' => $openid]);
            }

            // 微信授权登录获取openid
            $redirect = url_append_query(
                route('order.pay.wechat.jsapi'),
                [
                    'oauth' => 1,
                    'data' => $data,
                ]
            );
            return Wechat::getInstance()->oauth->redirect($redirect);
        }

        try {
            $decryptData = decrypt($data);

            $orderId = $decryptData['order_id'];
            $sUrl = $decryptData['s_url'];
            $fUrl = $decryptData['f_url'];

            if ($decryptData['expired_at'] < time()) {
                abort(406, __('参数错误'));
            }

            $order = $orderService->find($orderId);

            $data = $paymentHandler->create($order, ['openid' => $openid]);

            $title = __('微信支付');

            return view('h5.order.wechat-jsapi-pay', compact('order', 'title', 'data', 'sUrl', 'fUrl'));
        } catch (\Exception $e) {
            abort(406, __('参数错误'));
        }
    }
}

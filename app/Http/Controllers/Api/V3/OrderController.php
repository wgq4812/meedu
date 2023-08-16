<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use App\Meedu\Bus\Order\OrderHandler;
use App\Http\Controllers\Api\V2\BaseController;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;

class OrderController extends BaseController
{
    /**
     * @api {post} /api/v3/order 创建订单
     * @apiGroup 订单
     * @apiName OrderStoreV3
     * @apiVersion v3.0.0
     *
     * @apiParam {String=COURSE,ROLE} type 商品类型
     * @apiParam {Number} id 商品id
     * @apiParam {String} promo_code 优惠码
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.order_no 订单编号
     * @apiSuccess {Number} data.charge 订单总金额
     * @apiSuccess {Number} data.continue_pay_amount 需要支付的总金额
     * @apiSuccess {String} data.created_at 订单创建时间
     * @apiSuccess {Number} data.status 订单状态[1:未支付,5:支付中,7:已取消,9:已支付]
     */
    public function store(Request $request, OrderHandler $orderHandler)
    {
        $type = $request->input('type');
        $id = (int)$request->input('id');
        $promoCode = $request->input('promo_code');
        if (!$type || !$id) {
            return $this->error(__('参数错误'));
        }

        $order = $orderHandler->setType($type)->create($this->id(), $id, $promoCode);

        return $this->data([
            'order_no' => $order['order_id'],
            'charge' => $order['charge'],
            'created_at' => $order['created_at'],
            'continue_pay_amount' => $order['continue_pay_amount'],
            'status' => $order['status'],
        ]);
    }

    /**
     * @api {get} /api/v3/order/status 订单状态查询
     * @apiGroup 订单
     * @apiName OrderStoreStatusV3
     * @apiVersion v3.0.0
     *
     * @apiParam {String} order_no 订单编号
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.status 订单状态[1:未支付,5:支付中,7:已取消,9:已支付]
     */
    public function status(Request $request, OrderServiceInterface $orderService)
    {
        $orderNo = $request->input('order_no');
        if (!$orderNo) {
            return $this->error(__('参数错误'));
        }
        $status = $orderService->orderStatus($this->id(), $orderNo);
        return $this->data(compact('status'));
    }

    /**
     * @api {get} /api/v3/order/promoCode 优惠码检测
     * @apiGroup 订单
     * @apiName PromoCodeCheck
     * @apiVersion v3.0.0
     *
     * @apiParam {String} code 优惠码
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {Number=1:可以,0:不可以} data.can_use 是否可以使用
     * @apiSuccess {Object} data.promo_code
     * @apiSuccess {String} data.promo_code.code 优惠码
     * @apiSuccess {Number} data.promo_code.discount 优惠码面值
     */
    public function promoCode(Request $request, OrderServiceInterface $orderService)
    {
        $code = $request->input('code');
        if (!$code) {
            return $this->error(__('参数错误'));
        }
        $promoCode = $orderService->promoCode($code);

        $canUse = 0;
        $data = [];

        if ($promoCode) {
            $canUse = (int)$orderService->canUsePromoCode($this->id(), $promoCode);
            $data = [
                'code' => $promoCode['code'],
                'discount' => $promoCode['invite_user_reward'],
            ];
        }

        return $this->data([
            'can_use' => $canUse,
            'promo_code' => $data,
        ]);
    }
}

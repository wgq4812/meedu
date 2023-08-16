<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Tests\MeEdu\Service\Service;

use Carbon\Carbon;
use Tests\TestCase;
use App\Constant\FrontendConstant;
use App\Exceptions\ServiceException;
use App\Meedu\ServiceV2\Models\User;
use App\Meedu\ServiceV2\Models\Order;
use App\Meedu\ServiceV2\Models\PromoCode;
use App\Meedu\ServiceV2\Models\OrderGoods;
use App\Meedu\ServiceV2\Models\OrderPaidRecord;
use App\Meedu\ServiceV2\Models\UserPromoCodeRecord;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderServiceTest extends TestCase
{
    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    public function setUp(): void
    {
        parent::setUp();
        $this->orderService = $this->app->make(OrderServiceInterface::class);
    }

    public function test_find_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->orderService->find('random_order_no');
    }

    public function test_find_success()
    {
        $order = Order::factory()->create();
        $queryOrder = $this->orderService->find($order['order_id']);
        $this->assertEquals($order['order_id'], $queryOrder['order_id']);
    }

    public function test_findById_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->orderService->find(1);
    }

    public function test_findById_success()
    {
        $order = Order::factory()->create();
        $queryOrder = $this->orderService->findById($order['id']);
        $this->assertEquals($order['order_id'], $queryOrder['order_id']);
    }

    public function test_orderStatus()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user['id']]);
        $status = $this->orderService->orderStatus($order['user_id'], $order['order_id']);
        $this->assertEquals($order['status'], $status);
    }

    public function test_findUserOrder_not_found()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user['id']]);
        $this->expectException(ModelNotFoundException::class);
        $this->orderService->findUserOrder($user['id'] + 1, $order['order_id']);
    }

    public function test_findUserOrder_success()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user['id']]);
        Order::factory()->create(['user_id' => $user['id']]);
        $queryOrder = $this->orderService->findUserOrder($user['id'], $order['order_id']);
        $this->assertEquals($order['order_id'], $queryOrder['order_id']);
    }

    public function test_change2Paying_success()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user['id'], 'status' => FrontendConstant::ORDER_UN_PAY]);
        Order::factory()->create(['user_id' => $user['id']]);

        $this->orderService->change2Paying($user['id'], $order['id'], [
            'payment' => 'wechat',
            'payment_method' => 'wechat',
        ]);

        $order->refresh();

        $this->assertEquals(FrontendConstant::ORDER_PAYING, $order['status']);
        $this->assertEquals('wechat', $order['payment']);
        $this->assertEquals('wechat', $order['payment_method']);
    }

    public function test_change2Paying_failure()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user['id'], 'status' => FrontendConstant::ORDER_PAYING]);
        Order::factory()->create(['user_id' => $user['id']]);

        $this->expectException(ServiceException::class);
        $this->orderService->change2Paying($user['id'], $order['id'], [
            'payment' => 'wechat',
            'payment_method' => 'wechat',
        ]);
    }

    public function test_change2Paid_success()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user['id'], 'status' => FrontendConstant::ORDER_PAYING]);
        Order::factory()->create(['user_id' => $user['id']]);

        $this->orderService->change2Paid($order['id']);

        $order->refresh();

        $this->assertEquals(FrontendConstant::ORDER_PAID, $order['status']);
    }

    public function test_getOrderGoodsTitle()
    {
        $order = Order::factory()->create();
        $orderGoods = OrderGoods::factory()->create(['oid' => $order['id']]);
        $this->assertEquals($orderGoods['goods_name'], $this->orderService->getOrderGoodsTitle($order['id']));
    }

    public function test_orderGoodsList()
    {
        $order = Order::factory()->create();
        $orderGoods = OrderGoods::factory()->create(['oid' => $order['id']]);
        $list = $this->orderService->orderGoodsList($order['id']);
        $this->assertCount(1, $list);
        $this->assertEquals($orderGoods['goods_name'], $list[0]['goods_name']);
    }

    public function test_promoCode_not_found()
    {
        $code = $this->orderService->promoCode('not_found');
        $this->assertEmpty($code);
    }

    public function test_promoCode()
    {
        $promoCode = PromoCode::factory()->create();
        $code = $this->orderService->promoCode($promoCode['code']);
        $this->assertEquals($promoCode['code'], $code['code']);
    }

    public function test_canUsePromoCode()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['use_times' => 0]);
        $this->assertTrue($this->orderService->canUsePromoCode($user['id'], $promoCode->toArray()));
    }

    public function test_canUsePromoCode_failure_user_id()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['user_id' => $user['id']]);
        $this->assertFalse($this->orderService->canUsePromoCode($user['id'], $promoCode->toArray()));
    }

    public function test_canUsePromoCode_failure_use_times()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['use_times' => 1, 'used_times' => 1]);
        $this->assertFalse($this->orderService->canUsePromoCode($user['id'], $promoCode->toArray()));
    }

    public function test_canUsePromoCode_failure_used()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['use_times' => 1, 'used_times' => 1]);
        UserPromoCodeRecord::factory()->create(['user_id' => $user['id'], 'code_id' => $promoCode['id']]);

        $this->assertFalse($this->orderService->canUsePromoCode($user['id'], $promoCode->toArray()));
    }

    public function test_canUsePromoCode_failure_paid_record()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['use_times' => 1, 'used_times' => 1]);

        OrderPaidRecord::factory()->create([
            'user_id' => $user['id'],
            'order_id' => 1,
            'paid_total' => 0,
            'paid_type' => FrontendConstant::ORDER_PAID_TYPE_PROMO_CODE,
            'paid_type_id' => $promoCode['id'],
        ]);

        $this->assertFalse($this->orderService->canUsePromoCode($user['id'], $promoCode->toArray()));
    }

    public function test_storeOrder()
    {
        $user = User::factory()->create();
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], '');

        $this->assertEquals($user['id'], $order['user_id']);
        $this->assertEquals(100, $order['charge']);
        $this->assertEquals(FrontendConstant::ORDER_UN_PAY, $order['status']);
    }

    public function test_storeOrder_use_promoCode()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['user_id' => 0, 'invite_user_reward' => 10]);

        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], $promoCode['code']);

        $this->assertEquals($user['id'], $order['user_id']);
        $this->assertEquals(100, $order['charge']);
        $this->assertEquals(FrontendConstant::ORDER_UN_PAY, $order['status']);
    }

    public function test_storeOrder_use_promoCode_override_total()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['user_id' => 0, 'invite_user_reward' => 1000]);

        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], $promoCode['code']);

        $order = $this->orderService->findById($order['id']);

        $this->assertEquals($user['id'], $order['user_id']);
        $this->assertEquals(100, $order['charge']);
        $this->assertEquals(FrontendConstant::ORDER_PAID, $order['status']);
    }

    public function test_cancelOrder()
    {
        $user = User::factory()->create();
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], '');

        $this->orderService->cancelOrder($order);

        $order = $this->orderService->findById($order['id']);

        $this->assertEquals(FrontendConstant::ORDER_CANCELED, $order['status']);
    }

    public function test_cancelOrder_with_promo_code()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['user_id' => 0, 'invite_user_reward' => 50]);
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], $promoCode['code']);

        // 优惠码使用次数+1
        $promoCode = $this->orderService->promoCode($promoCode['code']);
        $this->assertEquals(1, $promoCode['used_times']);

        // 优惠码使用记录
        $userPromoCodeRecord = UserPromoCodeRecord::query()->where('user_id', $user['id'])->where('code_id', $promoCode['id'])->where('order_id', $order['id'])->first();
        $this->assertNotEmpty($userPromoCodeRecord);
        $this->assertEquals(50, $userPromoCodeRecord['discount']);
        $this->assertEquals(50, $userPromoCodeRecord['original_amount']);

        $this->orderService->cancelOrder($order);

        // 使用次数恢复
        $promoCode = $this->orderService->promoCode($promoCode['code']);
        $this->assertEquals(0, $promoCode['used_times']);

        // 使用记录删除
        $userPromoCodeRecord = UserPromoCodeRecord::query()->where('user_id', $user['id'])->where('code_id', $promoCode['id'])->where('order_id', $order['id'])->first();
        $this->assertEmpty($userPromoCodeRecord);
    }

    public function test_cancelOrder_failure_status_error()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['user_id' => 0, 'invite_user_reward' => 1000]);
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], $promoCode['code']);

        $order = $this->orderService->findById($order['id']);

        $this->expectExceptionMessage(__('订单状态错误'));
        $this->orderService->cancelOrder($order);
    }

    public function test_cancelOrder_failure_()
    {
        $user = User::factory()->create();
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], '');

        $this->orderService->change2Paid($order['id']);
        $this->expectExceptionMessage(__('系统错误'));
        $this->orderService->cancelOrder($order);
    }

    public function test_continuePayAmount()
    {
        $user = User::factory()->create();
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], '');
        $this->assertEquals(100, $this->orderService->continuePayAmount($order['id']));
    }

    public function test_continuePayAmount_with_promoCode()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['user_id' => 0, 'invite_user_reward' => 10]);
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], $promoCode['code']);
        $this->assertEquals(90, $this->orderService->continuePayAmount($order['id']));
    }

    public function test_continuePayAmount_with_promoCode_override()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['user_id' => 0, 'invite_user_reward' => 1000]);
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], $promoCode['code']);
        $this->assertEquals(0, $this->orderService->continuePayAmount($order['id']));
    }

    public function test_remainingAmountHandPay()
    {
        $user = User::factory()->create();
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], '');
        $this->orderService->remainingAmountHandPay($order['id']);

        $order = $this->orderService->findById($order['id']);
        $this->assertEquals(FrontendConstant::ORDER_PAID, $order['status']);

        // 支付记录
        $paidRecords = OrderPaidRecord::query()->where('order_id', $order['id'])->get()->toArray();
        $this->assertCount(1, $paidRecords);
        $this->assertEquals(FrontendConstant::ORDER_PAID_TYPE_HAND, $paidRecords[0]['paid_type']);
        $this->assertEquals(100, $paidRecords[0]['paid_total']);
    }

    public function test_remainingAmountHandPay_with_promoCode()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['user_id' => 0, 'invite_user_reward' => 20]);
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], $promoCode['code']);

        $this->orderService->remainingAmountHandPay($order['id']);

        $order = $this->orderService->findById($order['id']);
        $this->assertEquals(FrontendConstant::ORDER_PAID, $order['status']);

        // 支付记录
        $paidRecords = OrderPaidRecord::query()->where('order_id', $order['id'])->orderBy('id')->get()->toArray();
        $this->assertCount(2, $paidRecords);
        $this->assertEquals(FrontendConstant::ORDER_PAID_TYPE_HAND, $paidRecords[1]['paid_type']);
        $this->assertEquals(80, $paidRecords[1]['paid_total']);
    }

    public function test_remainingAmountHandPay_with_promoCode_failure()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create(['user_id' => 0, 'invite_user_reward' => 1000]);
        $order = $this->orderService->storeOrder($user['id'], 100, [
            [
                'goods_id' => 1,
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => 'meedu高级教程',
                'goods_thumb' => '',
                'goods_charge' => 100,
                'goods_ori_charge' => 299,
                'num' => 1,
                'charge' => 100,
            ],
        ], $promoCode['code']);

        $this->expectExceptionMessage(__('无需继续支付'));
        $this->orderService->remainingAmountHandPay($order['id']);
    }

    public function test_findOrderRefund()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->orderService->findOrderRefund('not_found');
    }

    public function test_getTimeoutOrders()
    {
        Order::factory()->count(3)->create([
            'created_at' => Carbon::now()->subDays(3)->toDateTimeLocalString(),
            'status' => FrontendConstant::ORDER_UN_PAY,
        ]);
        Order::factory()->count(5)->create();

        $this->assertCount(3, $this->orderService->getTimeoutOrders(Carbon::now()->subHours(1)->toDateTimeLocalString()));
    }

    public function test_takeProcessingRefundOrders()
    {
        $this->assertCount(0, $this->orderService->takeProcessingRefundOrders(10));
    }

    public function test_userOrdersPaginate()
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create(['user_id' => $user['id']]);
        $data = $this->orderService->userOrdersPaginate($user['id'], 1, 1);
        $this->assertEquals(3, $data['total']);
    }
}

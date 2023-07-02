<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Tests\Unit\Events;

use Tests\TestCase;
use App\Constant\FrontendConstant;
use App\Services\Member\Models\Role;
use App\Services\Member\Models\User;
use App\Meedu\Bus\Order\OrderHandler;
use App\Meedu\ServiceV2\Models\PromoCode;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;

class OrderCancelEventTest extends TestCase
{
    public function test_PromoCodeResumeListener()
    {
        $user = User::factory()->create();
        $promoCode = PromoCode::factory()->create([
            'used_times' => 1,
        ]);

        $role = Role::factory()->create(['charge' => 100]);

        /**
         * @var OrderHandler $orderHandler
         */
        $orderHandler = $this->app->make(OrderHandler::class);
        $order = $orderHandler->setType(FrontendConstant::ORDER_TYPE_ROLE)->create($user['id'], $role['id'], $promoCode['code']);

        /**
         * @var OrderServiceInterface $orderService
         */
        $orderService = $this->app->make(OrderServiceInterface::class);

        $orderService->cancelOrder($order);

        $promoCode->refresh();

        $this->assertEquals(1, $promoCode['used_times']);
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Bus\Order\Core;

use App\Constant\FrontendConstant;
use App\Meedu\ServiceV2\Services\RoleServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRole implements OrderInterface
{
    private $roleService;

    public function __construct(RoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
    }

    public function check(int $userId, array $ids): void
    {
        // role的重复购买无需检测
    }

    public function goodsList(array $ids): array
    {
        $roles = $this->roleService->chunks($ids);
        if (!$roles) {
            throw new ModelNotFoundException();
        }
        $data = [];
        foreach ($roles as $roleItem) {
            $data[] = [
                'goods_id' => $roleItem['id'],
                'goods_type' => FrontendConstant::ORDER_TYPE_ROLE,
                'goods_name' => $roleItem['name'],
                'goods_thumb' => '',
                'goods_charge' => $roleItem['charge'],
                'goods_ori_charge' => $roleItem['charge'],
                'num' => 1,
                'charge' => $roleItem['charge'],
            ];
        }
        return $data;
    }

    public function cancel(array $orderGoods): void
    {
    }

    public function refundConfirm(array $orderGoods): void
    {
    }
}

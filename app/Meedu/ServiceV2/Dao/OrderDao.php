<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

use App\Meedu\ServiceV2\Models\Order;

class OrderDao implements OrderDaoInterface
{
    public function find(array $params): array
    {
        return Order::query()
            ->when(isset($params['id']), function ($query) use ($params) {
                $query->where('id', $params['id']);
            })
            ->when(isset($params['order_id']), function ($query) use ($params) {
                $query->where('order_id', $params['order_id']);
            })
            ->when(isset($params['user_id']), function ($query) use ($params) {
                $query->where('user_id', $params['user_id']);
            })
            ->firstOrFail()
            ->toArray();
    }

    public function update(array $data, array $params): int
    {
        return Order::query()
            ->when(isset($params['id']), function ($query) use ($params) {
                $query->where('id', $params['id']);
            })
            ->when(isset($params['order_id']), function ($query) use ($params) {
                $query->where('order_id', $params['order_id']);
            })
            ->when(isset($params['user_id']), function ($query) use ($params) {
                $query->where('user_id', $params['user_id']);
            })
            ->when(isset($params['status']), function ($query) use ($params) {
                $query->where('status', $params['status']);
            })
            ->update($data);
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

use App\Meedu\ServiceV2\Models\MpWechatMessageReply;

class MpWechatDao implements MpWechatDaoInterface
{
    public function get(array $filter, array $fields): array
    {
        return MpWechatMessageReply::query()
            ->select($fields)
            ->when($fields, function ($query) use ($filter) {
                if (isset($filter['type'])) {
                    $query->where('type', $filter['type']);
                }
                if (isset($filter['event_type'])) {
                    $query->where('event_type', $filter['event_type']);
                }
            })
            ->orderByDesc('id')
            ->get()
            ->toArray();
    }


}

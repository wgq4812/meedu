<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

use App\Meedu\ServiceV2\Models\Role;

class RoleDao implements RoleDaoInterface
{
    public function findOrFail(int $id)
    {
        return Role::query()->where('id', $id)->firstOrFail()->toArray();
    }

    public function all(): array
    {
        return Role::query()->orderBy('weight')->get()->toArray();
    }

    public function chunks(array $ids): array
    {
        return Role::query()->whereIn('id', $ids)->orderBy('weight')->get()->toArray();
    }
}

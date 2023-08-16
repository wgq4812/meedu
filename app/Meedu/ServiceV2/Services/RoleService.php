<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Meedu\ServiceV2\Dao\RoleDaoInterface;

class RoleService implements RoleServiceInterface
{
    private $roleDao;

    public function __construct(RoleDaoInterface $roleDao)
    {
        $this->roleDao = $roleDao;
    }

    public function findOrFail(int $id): array
    {
        return $this->roleDao->findOrFail($id);
    }

    public function all(): array
    {
        return $this->roleDao->all();
    }

    public function chunks(array $ids): array
    {
        return $this->roleDao->chunks($ids);
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Meedu\ServiceV2\Dao\RoleDaoInterface;
use App\Meedu\ServiceV2\Services\Traits\HashIdTrait;

class RoleService implements RoleServiceInterface
{
    use HashIdTrait;

    private $roleDao;

    public function __construct(RoleDaoInterface $roleDao)
    {
        $this->roleDao = $roleDao;
    }

    public function all(): array
    {
        $data = $this->roleDao->all();
        if ($data) {
            foreach ($data as $key => $item) {
                $data[$key]['id'] = $this->idEncode($item['id']);
            }
        }
        return $data;
    }

    public function chunks(array $ids): array
    {
        return $this->roleDao->chunks($ids);
    }
}

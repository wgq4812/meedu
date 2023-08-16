<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

interface RoleDaoInterface
{
    public function findOrFail(int $id);

    public function all(): array;

    public function chunks(array $ids): array;
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

interface RuntimeStatusDaoInterface
{
    public function save(string $name, $status);

    public function nameChunks(array $names): array;
}

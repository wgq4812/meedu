<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

interface ConfigDaoInterface
{

    public function updateByKey(string $key, string $value): void;

    public function all(): array;

}

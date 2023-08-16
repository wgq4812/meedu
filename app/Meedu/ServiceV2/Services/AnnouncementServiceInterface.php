<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface AnnouncementServiceInterface
{

    public function paginate(int $page, int $size): array;

    public function find(int $id): array;

    public function idEncode(int $id): string;

    public function idDecode(string $str): int;
}

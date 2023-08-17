<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services\Traits;

use Hashids\Hashids;

trait HashIdTrait
{

    public function idEncode(int $id, int $length = 5): string
    {
        $hashId = new Hashids(__CLASS__, $length);
        return $hashId->encode($id);
    }

    public function idDecode(string $str, int $length = 5): int
    {
        $hashId = new Hashids(__CLASS__, $length);
        return (int)($hashId->decode($str)[0] ?? 0);
    }

}

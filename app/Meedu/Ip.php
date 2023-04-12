<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu;

class Ip
{
    public static function ip2area($ip)
    {
        $ip2Region = new \Ip2Region();
        return $ip2Region->simple($ip);
    }
}

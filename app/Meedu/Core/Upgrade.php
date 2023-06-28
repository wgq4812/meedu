<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Core;

use App\Meedu\Core\UpgradeLog\UpgradeTo48;
use App\Meedu\Core\UpgradeLog\UpgradeToV4;
use App\Meedu\Core\UpgradeLog\UpgradeToV42;
use App\Meedu\Core\UpgradeLog\UpgradeToV45;
use App\Meedu\Core\UpgradeLog\UpgradeToV46;
use App\Meedu\Core\UpgradeLog\UpgradeToV454;
use App\Meedu\Core\UpgradeLog\UpgradeToV500;

class Upgrade
{
    public function run()
    {
        UpgradeToV4::handle();
        UpgradeToV42::handle();
        UpgradeToV45::handle();
        UpgradeToV454::handle();
        UpgradeToV46::handle();
        UpgradeTo48::handle();
        UpgradeToV500::handle();
    }
}

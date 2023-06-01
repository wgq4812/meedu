<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Meedu\UpgradeLog\UpgradeToV45;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SearchImportCommand extends Command
{
    protected $signature = 'meedu:search:import';

    protected $description = '全文搜索数据导入';

    public function handle(): int
    {
        UpgradeToV45::courseAndVideoMigrateMeiliSearch();
        return CommandAlias::SUCCESS;
    }
}

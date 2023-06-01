<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Meedu\ServiceV2\Services\RuntimeStatusServiceInterface;

class ScheduleListenerCommand extends Command
{
    protected $signature = 'meedu:schedule:listener';

    protected $description = 'MeEdu定时任务的监听命令';

    public function handle()
    {
        /**
         * @var RuntimeStatusServiceInterface $runtimeStatusService
         */
        $runtimeStatusService = app()->make(RuntimeStatusServiceInterface::class);
        $runtimeStatusService->updateSchedule(time());
        return Command::SUCCESS;
    }
}

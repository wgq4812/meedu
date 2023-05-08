<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Meedu\ServiceV2\Services\UserServiceInterface;
use Symfony\Component\Console\Command\Command as CommandAlias;

class UserDeleteJobRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meedu:user-delete-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用户注销任务执行程序';

    public function handle(): int
    {
        /**
         * @var UserServiceInterface $userService
         */
        $userService = app()->make(UserServiceInterface::class);
        $userService->userDeleteBatchHandle();

        return CommandAlias::SUCCESS;
    }
}

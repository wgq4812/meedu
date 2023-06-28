<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Console\Commands;

use App\Meedu\MeEdu;
use App\Meedu\Core\Upgrade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as CommandAlias;
use App\Meedu\ServiceV2\Services\RuntimeStatusServiceInterface;

class MeEduUpgradeCommand extends Command
{
    protected $signature = 'meedu:upgrade';

    protected $description = 'MeEdu升级处理命令';

    public function handle(): int
    {
        /**
         * @var RuntimeStatusServiceInterface $rsService
         */
        $rsService = app()->make(RuntimeStatusServiceInterface::class);

        $this->line('当前系统版本:' . $rsService->getSystemVersion());
        $this->line('本地系统版本:' . MeEdu::VERSION);

        // 数据库迁移命令
        $this->info('执行数据库迁移...');
        Artisan::call('migrate', ['--force' => true]);

        // 同步meedu最新配置
        $this->info('同步最新配置...');
        Artisan::call('install', ['action' => 'config']);

        // 同步管理角色和权限
        $this->info('同步后台管理权限...');
        Artisan::call('install', ['action' => 'role']);

        // 执行升级业务逻辑
        $this->info('执行升级业务逻辑...');
        (new Upgrade)->run();

        // 清空路由缓存
        $this->info('清除路由缓存...');
        Artisan::call('route:clear');

        // 清空配置缓存
        $this->info('清除配置缓存...');
        Artisan::call('config:clear');

        // 清空视图缓存
        $this->info('清除视图缓存...');
        Artisan::call('view:clear');

        $this->info('更新版本中...');
        try {
            $rsService->setSystemVersion(MeEdu::VERSION);
        } catch (\Exception $e) {
            $this->error(__('系统版本更新失败,错误信息 :msg', ['msg' => $e->getMessage()]));
            return Command::FAILURE;
        }

        $this->info('升级成功');

        return CommandAlias::SUCCESS;
    }
}

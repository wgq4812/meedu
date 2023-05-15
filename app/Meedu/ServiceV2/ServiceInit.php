<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2;

use App\Meedu\ServiceV2\Dao\UserDao;
use App\Meedu\ServiceV2\Dao\OtherDao;
use App\Meedu\ServiceV2\Dao\CourseDao;
use App\Meedu\ServiceV2\Dao\RuntimeStatusDao;
use App\Meedu\ServiceV2\Dao\UserDaoInterface;
use App\Meedu\ServiceV2\Services\UserService;
use App\Meedu\ServiceV2\Dao\OtherDaoInterface;
use App\Meedu\ServiceV2\Services\OtherService;
use App\Meedu\ServiceV2\Dao\CourseDaoInterface;
use App\Meedu\ServiceV2\Services\ConfigService;
use App\Meedu\ServiceV2\Services\CourseService;
use App\Meedu\ServiceV2\Services\SettingService;
use App\Meedu\ServiceV2\Services\TencentVodService;
use App\Meedu\ServiceV2\Dao\RuntimeStatusDaoInterface;
use App\Meedu\ServiceV2\Services\UserServiceInterface;
use App\Meedu\ServiceV2\Services\OtherServiceInterface;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;
use App\Meedu\ServiceV2\Services\CourseServiceInterface;
use App\Meedu\ServiceV2\Services\SettingServiceInterface;
use App\Meedu\ServiceV2\Services\RuntimeStatusStatusService;
use App\Meedu\ServiceV2\Services\TencentVodServiceInterface;
use App\Meedu\ServiceV2\Services\RuntimeStatusServiceInterface;

class ServiceInit
{
    public $dao = [
        UserDaoInterface::class => UserDao::class,
        CourseDaoInterface::class => CourseDao::class,
        OtherDaoInterface::class => OtherDao::class,
        RuntimeStatusDaoInterface::class => RuntimeStatusDao::class,
    ];

    public $service = [
        ConfigServiceInterface::class => ConfigService::class,
        OtherServiceInterface::class => OtherService::class,
        UserServiceInterface::class => UserService::class,
        CourseServiceInterface::class => CourseService::class,
        TencentVodServiceInterface::class => TencentVodService::class,
        SettingServiceInterface::class => SettingService::class,
        RuntimeStatusServiceInterface::class => RuntimeStatusStatusService::class,
    ];

    public function run()
    {
        foreach ($this->dao as $interface => $class) {
            app()->instance($interface, app()->make($class));
        }

        foreach ($this->service as $interface => $class) {
            app()->instance($interface, app()->make($class));
        }
    }
}

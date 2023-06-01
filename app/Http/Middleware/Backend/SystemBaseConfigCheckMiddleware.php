<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Middleware\Backend;

use Closure;
use App\Meedu\MeEdu;
use Illuminate\Http\Request;
use App\Exceptions\SystemBaseConfigErrorException;
use App\Http\Controllers\Backend\Traits\ResponseTrait;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;
use App\Meedu\ServiceV2\Services\RuntimeStatusServiceInterface;

class SystemBaseConfigCheckMiddleware
{
    use ResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        /**
         * @var ConfigServiceInterface $configService
         */
        $configService = app()->make(ConfigServiceInterface::class);

        if ($configService->isEnvTest()) {
            return $next($request);
        }

        /**
         * @var RuntimeStatusServiceInterface $rsService
         */
        $rsService = app()->make(RuntimeStatusServiceInterface::class);
        $version = $rsService->getSystemVersion();
        if ($version != MeEdu::VERSION) {
            throw new SystemBaseConfigErrorException(__('请升级MeEdu系统'));
        }

        $scheduleTime = $rsService->getScheduleValue();
        if (time() - $scheduleTime > 10) {//误差超过10s
            throw new SystemBaseConfigErrorException(__('未配置定时任务'));
        }

        if (!$configService->isEnabledRedisCache()) {
            throw new SystemBaseConfigErrorException(__('未配置redis缓存'));
        }
        if (!$configService->isEnabledRedisQueue()) {
            throw new SystemBaseConfigErrorException(__('未配置redis队列'));
        }

        return $next($request);
    }
}

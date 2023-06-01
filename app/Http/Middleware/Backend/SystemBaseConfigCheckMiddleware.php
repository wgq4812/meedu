<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Middleware\Backend;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\SystemBaseConfigErrorException;
use App\Http\Controllers\Backend\Traits\ResponseTrait;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

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

        if (!$configService->isEnabledRedisCache()) {
            throw new SystemBaseConfigErrorException(__('未配置redis缓存'));
        }
        if (!$configService->isEnabledRedisQueue()) {
            throw new SystemBaseConfigErrorException(__('未配置redis队列'));
        }

        return $next($request);
    }
}

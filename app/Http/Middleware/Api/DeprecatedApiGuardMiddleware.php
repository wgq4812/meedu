<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class DeprecatedApiGuardMiddleware
{
    protected $configService;

    public function __construct(ConfigServiceInterface $configService)
    {
        $this->configService = $configService;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->configService->isCloseDeprecatedApi()) {
            abort(404);
        }
        return $next($request);
    }
}

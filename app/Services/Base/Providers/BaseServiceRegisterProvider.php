<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Services\Base\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Base\Services\CacheService;
use App\Services\Base\Interfaces\CacheServiceInterface;

class BaseServiceRegisterProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->instance(CacheServiceInterface::class, $this->app->make(CacheService::class));
    }
}

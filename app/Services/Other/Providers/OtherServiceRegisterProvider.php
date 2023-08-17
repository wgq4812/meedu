<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Services\Other\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Other\Services\MpWechatService;
use App\Services\Other\Services\SearchRecordService;
use App\Services\Other\Interfaces\MpWechatServiceInterface;
use App\Services\Other\Interfaces\SearchRecordServiceInterface;

class OtherServiceRegisterProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->instance(MpWechatServiceInterface::class, $this->app->make(MpWechatService::class));
        $this->app->instance(SearchRecordServiceInterface::class, $this->app->make(SearchRecordService::class));
    }
}

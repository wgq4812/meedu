<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Services\Course\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Course\Services\VideoService;
use App\Services\Course\Services\CourseService;
use App\Services\Course\Services\CourseCommentService;
use App\Services\Course\Services\CourseCategoryService;
use App\Services\Course\Interfaces\VideoServiceInterface;
use App\Services\Course\Interfaces\CourseServiceInterface;
use App\Services\Course\Interfaces\CourseCommentServiceInterface;
use App\Services\Course\Interfaces\CourseCategoryServiceInterface;

class CourseServiceRegisterProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->instance(CourseServiceInterface::class, $this->app->make(CourseService::class));
        $this->app->instance(VideoServiceInterface::class, $this->app->make(VideoService::class));
        $this->app->instance(CourseCommentServiceInterface::class, $this->app->make(CourseCommentService::class));
        $this->app->instance(CourseCategoryServiceInterface::class, $this->app->make(CourseCategoryService::class));
    }
}

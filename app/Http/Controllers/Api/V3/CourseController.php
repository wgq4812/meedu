<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Meedu\Cache\Impl\CourseCategoryCache;
use App\Meedu\ServiceV2\Services\CourseServiceInterface;

class CourseController extends BaseController
{
    public function index(Request $request, CourseServiceInterface $courseService, CourseCategoryCache $categoryCache)
    {
        $page = (int)$request->input('page', 1);
        $size = (int)$request->input('size', 10);
        $categoryId = (int)$request->input('category_id');
        $isFree = (int)$request->input('is_free');

        // 条件过滤
        $params = [
            'is_show' => 1,//非隐藏课程
            'lte_published_at' => Carbon::now()->toDateTimeLocalString(),//已上架课程
        ];
        $isFree === 1 && $params['is_free'] = 1;//筛选免费课程
        $categoryId > 0 && $params['category_id'] = $categoryId;//分类筛选

        $data = $courseService->coursePaginate($page, $size, $params, [], ['videos', 'chapters']);

        return $this->data([
            'courses' => $data,
            'categories' => $categoryCache->get(),
        ]);
    }
}

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
    /**
     * @api {get} /api/v3/course/categories 课程分类
     * @apiGroup Course-V3
     * @apiName V3-CourseCategories
     * @apiVersion v3.0.0
     * @apiDescription v5.0新增
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.id 分类id
     * @apiSuccess {String} data.name 分类名
     * @apiSuccess {Object[]} data.children 子分类
     * @apiSuccess {String} data.children.id 子分类ID
     * @apiSuccess {String} data.children.name 子分类名
     */
    public function categories(CourseServiceInterface $courseService)
    {
        return $this->data($courseService->categoriesWithChildren());
    }

    /**
     * @api {get} /api/v3/courses 录播课列表
     * @apiGroup Course-V3
     * @apiName V3-Courses
     * @apiVersion v3.0.0
     *
     * @apiParam {Number} [page] page
     * @apiParam {Number} [size] size
     * @apiParam {Number} [category_id] 分类id
     * @apiParam {Number=0,1} [is_free] 是否免费
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {Object[]} data.categories 课程分类
     * @apiSuccess {String} data.categories.id 分类id
     * @apiSuccess {String} data.categories.name 分类名
     * @apiSuccess {Object[]} data.categories.children 子分类
     * @apiSuccess {Object} data.courses 课程
     * @apiSuccess {Number} data.courses.total 课程总数
     * @apiSuccess {Object[]} data.courses.data 课程列表
     * @apiSuccess {String} data.courses.data.id 课程id
     * @apiSuccess {String} data.courses.data.title 课程名
     * @apiSuccess {String} data.courses.data.thumb 封面
     * @apiSuccess {Number} data.courses.data.charge 价格
     * @apiSuccess {String} data.courses.data.category_id 分类id
     * @apiSuccess {Number} data.courses.data.user_count 购买人数
     * @apiSuccess {Number=0,1} data.courses.data.is_free 是否免费
     * @apiSuccess {String} data.courses.data.short_description 简短描述
     * @apiSuccess {String} data.courses.data.published_at 上架时间
     */
    public function index(Request $request, CourseServiceInterface $courseService, CourseCategoryCache $categoryCache)
    {
        $page = (int)$request->input('page', 1);
        $size = (int)$request->input('size', 10);
        $categoryId = (int)$request->input('category_id');
        $isFree = (int)$request->input('is_free');

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

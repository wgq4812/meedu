<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Meedu\ServiceV2\Services\AnnouncementServiceInterface;

class AnnouncementController extends BaseController
{

    /**
     * @api {get} /api/v3/announcements 公告列表
     * @apiGroup 其它
     * @apiName Announcements
     * @apiVersion v2.0.0
     *
     * @apiParam {Number=1} [page] page
     * @apiParam {Number=10} [size] size
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object[]} data 数据
     * @apiSuccess {String} data.announcement 公告内容
     * @apiSuccess {String} data.title 标题
     * @apiSuccess {Number} data.view_times 浏览次数
     * @apiSuccess {String} data.created_at 创建时间
     */
    public function index(Request $request, AnnouncementServiceInterface $service)
    {
        $page = (int)$request->input('page', 1);
        $size = (int)$request->input('size', 10);

        $data = $service->paginate($page, $size);
        $data['data'] = arr2_clear($data['data'], ['id', 'announcement', 'title', 'view_times', 'created_at']);

        return $this->success($data);
    }

    /**
     * @api {get} /api/v2/announcement/{slug} 公告详情
     * @apiGroup 其它
     * @apiName AnnouncementDetail
     * @apiVersion v2.0.0
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.announcement 公告内容
     * @apiSuccess {String} data.title 标题
     * @apiSuccess {Number} data.view_times 浏览次数
     * @apiSuccess {String} data.created_at 时间
     */
    public function detail(AnnouncementServiceInterface $service, $slug)
    {
        $id = $service->idDecode($slug);
        $data = $service->find($id);
        if (!$data) {
            throw new ModelNotFoundException();
        }
        $data = arr1_clear($data, ['id', 'announcement', 'title', 'view_times', 'created_at']);
        return $this->data($data);
    }

}

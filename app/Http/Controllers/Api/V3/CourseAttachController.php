<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use App\Businesses\BusinessState;
use App\Http\Controllers\Api\V2\BaseController;
use App\Meedu\ServiceV2\Services\CourseServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CourseAttachController extends BaseController
{

    /**
     * @api {get} /api/v3/course/{courseId}/attach/{id}/download-url 获取课件下载URL
     * @apiGroup 录播课
     * @apiName CourseAttachDownloadV3
     * @apiVersion v3.0.0
     *
     * @apiParam {String} token 登录token
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.download_url 下载地址
     */
    public function getDownloadUrl(CourseServiceInterface $courseService, BusinessState $businessState, $courseId, $id)
    {
        $attachment = $courseService->findAttachment((int)$id, (int)$courseId);
        if (!$attachment) {
            throw new ModelNotFoundException();
        }
        if (!$businessState->isBuyCourse($this->id(), $attachment['course_id'])) {
            return $this->error(__('无权限'));
        }
        $courseService->attachmentDownloadTimesInc($attachment['id']);
        $data = ['expired_time' => time() + 3600];
        $sign = encrypt($data);
        return $this->data([
            'download_url' => route('course.attachment.download', ['courseId' => $attachment['course_id'], 'id' => $attachment['id'], 'sign' => $sign]),
        ]);
    }

    public function download(Request $request, CourseServiceInterface $courseService, $courseId, $id)
    {
        $sign = $request->input('sign');
        if (!$sign) {
            return $this->error(__('参数错误'));
        }
        $data = decrypt($sign);
        if (!$data) {
            return $this->error(__('参数错误'));
        }
        if (time() > $data['expired_time']) {
            return $this->error(__('已过期'));
        }
        $attachment = $courseService->findAttachment((int)$id, (int)$courseId);
        $path = storage_path('app / attach / ' . $attachment['path']);
        if (!file_exists($path)) {
            return $this->error(__('课件源文件已被删除'));
        }
        return response()->download(storage_path('app / attach / ' . $attachment['path']));
    }

}

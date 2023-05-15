<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use Carbon\Carbon;
use App\Meedu\Tencent\Vod as TencentVod;
use App\Http\Controllers\Api\V2\BaseController;
use App\Meedu\ServiceV2\Services\CourseServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VideoController extends BaseController
{
    public function tencentPlay(CourseServiceInterface $courseService, TencentVod $vod, $courseId, $videoId)
    {
        $course = $courseService->findOrFail($courseId);
        if (Carbon::now()->lte($course['published_at'])) {
            throw new ModelNotFoundException();
        }
        $video = $courseService->videoFindOrFail($videoId, $course['id']);
        if (Carbon::now()->lte($video['published_at'])) {
            throw new ModelNotFoundException();
        }
        $fileId = $video['tencent_video_id'];
        if (!$fileId) {
            return $this->error(__('参数错误'));
        }
        // todo 试看
        $sign = $vod->getPlaySign($fileId, false);
        return $this->data();
    }
}

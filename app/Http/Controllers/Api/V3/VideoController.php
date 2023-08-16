<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use Carbon\Carbon;
use App\Bus\VodPlayBus;
use App\Businesses\BusinessState;
use App\Http\Controllers\Api\V2\BaseController;
use App\Meedu\ServiceV2\Services\CourseServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VideoController extends BaseController
{
    public function play(CourseServiceInterface $courseService, VodPlayBus $vodPlayBus, BusinessState $state, $courseId, $videoId)
    {
        $course = $courseService->findOrFail($courseId);
        if (Carbon::now()->lte($course['published_at'])) {
            throw new ModelNotFoundException();
        }
        $video = $courseService->videoFindOrFail($videoId, $course['id']);
        if (Carbon::now()->lte($video['published_at'])) {
            throw new ModelNotFoundException();
        }

        $canSeeVideo = $state->canSeeVideo($this->user(), $course, $video);

        // 无法观看 && 未开启试看
        if (!$canSeeVideo && $video['free_seconds']) {
            return $this->error(__('当前视频无法观看'));
        }

        $trySeconds = $canSeeVideo ? 0 : $video['free_seconds'];

        $data = $vodPlayBus->getPlayInfo($video, $trySeconds);

        return $this->data($data);
    }
}

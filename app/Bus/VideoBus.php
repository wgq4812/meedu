<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Bus;

use App\Services\Member\Services\UserService;
use App\Services\Course\Services\VideoService;
use App\Meedu\Cache\Impl\UserLastWatchTimeCache;
use App\Services\Member\Interfaces\UserServiceInterface;
use App\Services\Course\Interfaces\VideoServiceInterface;

class VideoBus
{

    /**
     * @var VideoService
     */
    protected $videoService;

    public function __construct(VideoServiceInterface $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * 用户视频观看时长记录
     * @param int $userId
     * @param int $videoId
     * @param int $duration
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function userVideoWatchDurationRecord(int $userId, int $videoId, int $duration): void
    {
        // 查找观看的视频
        $video = $this->videoService->find($videoId);
        // 计算是否看完
        $isWatched = $video['duration'] <= $duration;

        $userLastWatchTimeCache = new UserLastWatchTimeCache($userId);
        $lastSubmitTimestamp = $userLastWatchTimeCache->get();

        /**
         * @var UserService $userService
         */
        $userService = app()->make(UserServiceInterface::class);

        // 用户每天的观看时间统计
        // 此方法要求前端没10s就需要请求一次
        $nowTime = microtime(true);
        if (($nowTime - $lastSubmitTimestamp) >= 9500) {
            $userService->watchStatSave($userId, 10);
            $userLastWatchTimeCache->put($nowTime);
        }

        $userService->recordUserVideoWatch($userId, $video['course_id'], $videoId, $duration, $isWatched);
    }
}

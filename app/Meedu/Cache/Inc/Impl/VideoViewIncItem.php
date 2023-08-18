<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache\Inc\Impl;

use App\Meedu\Cache\Inc\IncItem;
use App\Services\Course\Services\VideoService;
use App\Services\Course\Interfaces\VideoServiceInterface;

class VideoViewIncItem extends IncItem
{
    protected $videoId;

    protected $inc = 1;
    protected $limit = 100;

    public function __construct($videoId)
    {
        $this->videoId = $videoId;
    }

    public function getKey(): string
    {
        return sprintf('video-view:%d', $this->videoId);
    }

    public function save(): void
    {
        /**
         * @var VideoService $videoService
         */
        $videoService = app()->make(VideoServiceInterface::class);
        $videoService->viewNumIncrement($this->videoId, $this->limit);
    }
}

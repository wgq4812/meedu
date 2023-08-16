<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\AliVodCallbackEvent;

use App\Constant\CacheConstant;
use App\Constant\FrontendConstant;
use App\Events\VideoUploadedEvent;
use App\Events\AliVodCallbackEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Course\Models\MediaVideo;
use Illuminate\Contracts\Queue\ShouldQueue;

class VideoCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(AliVodCallbackEvent $event)
    {
        if ('success' !== $event->params['Status']) {
            return;
        }

        if ('MediaBaseChangeComplete' === $event->event) {
            $mediaId = $event->params['MediaId'];
            $mediaType = $event->params['MediaType'];
            $operateMode = $event->params['OperateMode'];
            if ('video' === $mediaType && 'create' == $operateMode) {
                $title = json_decode($event->params['MediaContent']['Title'], true);
                $title = $title['NewValue'];
                $title = str_ireplace('.mp4', '', $title);

                // 写入缓存
                $cacheKey = get_cache_key(CacheConstant::ALI_VOD_CALLBACK_VIDEO['name'], $mediaId);
                Cache::put($cacheKey, $title, CacheConstant::ALI_VOD_CALLBACK_VIDEO['expire']);
            }
        } elseif ('VideoAnalysisComplete' === $event->event) {
            $videoId = $event->params['VideoId'];

            $cacheKey = get_cache_key(CacheConstant::ALI_VOD_CALLBACK_VIDEO['name'], $videoId);
            $title = Cache::get($cacheKey);
            if (!$title) {
                Log::error(__METHOD__ . '|视频' . $videoId . '的标题不存在');
                return;
            }

            $exists = MediaVideo::query()
                ->where('storage_driver', FrontendConstant::VOD_SERVICE_ALIYUN)
                ->where('storage_file_id', $videoId)
                ->exists();
            if ($exists) {
                return;
            }

            $duration = (int)$event->params['Duration'];
            $size = (int)$event->params['Size'];

            $mediaVideo = MediaVideo::create([
                'title' => $title,
                'thumb' => '',
                'duration' => $duration,
                'size' => $size,
                'storage_driver' => FrontendConstant::VOD_SERVICE_ALIYUN,
                'storage_file_id' => $videoId,
            ]);

            event(new VideoUploadedEvent($mediaVideo['storage_file_id'], $mediaVideo['storage_driver'], 'callback', ''));

            Cache::forget($cacheKey);
        }
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\TencentVodCallbackEvent;

use App\Constant\FrontendConstant;
use App\Events\TencentVodCallbackEvent;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Course\Models\MediaVideo;
use Illuminate\Contracts\Queue\ShouldQueue;

class VideoCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;
    
    public function handle(TencentVodCallbackEvent $event)
    {
        if ('NewFileUpload' !== $event->event) {
            return;
        }
        $params = $event->params['FileUploadEvent'];
        $fileId = $params['FileId'];
        $name = str_ireplace('.mp4', '', $params['MediaBasicInfo']['Name']);
        $size = $params['MediaBasicInfo']['Size'];
        $duration = (int)$params['MetaData']['Duration'];

        $exists = MediaVideo::query()
            ->where('storage_driver', FrontendConstant::VOD_SERVICE_TENCENT)
            ->where('storage_file_id', $fileId)
            ->exists();
        if ($exists) {
            return;
        }

        MediaVideo::create([
            'title' => $name,
            'thumb' => '',
            'duration' => $duration,
            'size' => $size,
            'storage_driver' => FrontendConstant::VOD_SERVICE_TENCENT,
            'storage_file_id' => $fileId,
        ]);
    }
}

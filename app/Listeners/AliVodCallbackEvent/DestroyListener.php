<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\AliVodCallbackEvent;

use App\Constant\FrontendConstant;
use App\Events\AliVodCallbackEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Course\Models\MediaVideo;
use Illuminate\Contracts\Queue\ShouldQueue;

class DestroyListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(AliVodCallbackEvent $event)
    {
        if ('DeleteMediaComplete' !== $event->event) {
            return;
        }
        Log::info(__METHOD__, ['params' => $event->params]);
        if ('success' !== $event->params['Status']) {
            return;
        }
        if ('all' !== $event->params['DeleteType']) {
            return;
        }
        $mediaId = $event->params['MediaId'];

        MediaVideo::query()
            ->where('storage_driver', FrontendConstant::VOD_SERVICE_ALIYUN)
            ->where('storage_file_id', $mediaId)
            ->delete();
    }
}

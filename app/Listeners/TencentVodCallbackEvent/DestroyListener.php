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

class DestroyListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TencentVodCallbackEvent $event)
    {
        if ('FileDeleted' !== $event->event) {
            return;
        }

        $fileIds = $event->params['FileDeleteEvent']['FileIdSet'];
        if (!$fileIds) {
            return;
        }

        MediaVideo::query()
            ->where('storage_driver', FrontendConstant::VOD_SERVICE_TENCENT)
            ->whereIn('storage_file_id', $fileIds)
            ->delete();
    }
}

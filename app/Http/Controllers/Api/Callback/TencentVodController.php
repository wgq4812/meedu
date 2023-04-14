<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\Callback;

use Illuminate\Http\Request;
use App\Events\TencentVodCallbackEvent;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class TencentVodController
{
    public function handle(Request $request, ConfigServiceInterface $configService, $key)
    {
        if (!$key || $key !== $configService->getTencentVodCallbackKey()) {
            abort(403);
        }

        $event = $request->input('EventType');

        event(new TencentVodCallbackEvent($event, $request->all()));

        return 'success';
    }
}

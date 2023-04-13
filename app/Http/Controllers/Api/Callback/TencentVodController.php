<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\Callback;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class TencentVodController
{
    public function handle(Request $request, ConfigServiceInterface $configService, $key)
    {
        $params = $request->all();
        $body = $request->getContent();

        Log::info(__METHOD__, compact('params', 'body'));

        if (!$key || $key !== $configService->getTencentVodCallbackKey()) {
            abort(403);
        }

        return 'success';
    }
}

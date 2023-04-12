<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\Callback;

use Illuminate\Http\Request;
use App\Events\AliVodCallbackEvent;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class AliVodController
{
    public function handle(Request $request, ConfigServiceInterface $configService)
    {
        $url = $request->fullUrl();
        $timestamp = (int)$request->header('X-VOD-TIMESTAMP');
        $vodSign = strtolower($request->header('X-VOD-SIGNATURE', ''));

        // 密钥校验
        $key = $configService->getAliCallbackKey();
        $localSign = md5($url . '|' . $timestamp . '|' . $key);
        if ($localSign !== $vodSign) {
//            abort(403);
        }

        // 事件类型参考:https://help.aliyun.com/document_detail/55627.html
        $eventType = $request->input('EventType');
        $params = $request->all();

        event(new AliVodCallbackEvent($eventType, $timestamp, $params));

        return 'success';
    }
}

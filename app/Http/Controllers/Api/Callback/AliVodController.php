<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\Callback;

use App\Events\AliVodCallbackEvent;
use App\Http\Controllers\Api\Traits\ResponseTrait;
use App\Meedu\ServiceV2\Services\AliVodServiceInterface;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AliVodController
{
    use ResponseTrait;

    public function hls(Request $request, AliVodServiceInterface $avService)
    {
        $key = $request->input('Ciphertext');
        if (!$key) {
            return $this->error(__('参数错误'));
        }

        $cacheKeyName = md5($key);
        $text = Cache::get($cacheKeyName);
        if (!$text) {
            $text = $avService->decryptKMSDataKey($key);
            Cache::forever($cacheKeyName, $text);
        }

        return base64_decode($text);
    }

    public function handle(Request $request, ConfigServiceInterface $configService)
    {
        $url = $request->fullUrl();
        $timestamp = (int)$request->header('X-VOD-TIMESTAMP');
        $vodSign = strtolower($request->header('X-VOD-SIGNATURE', ''));

        // 密钥校验
        $key = $configService->getAliCallbackKey();
        $localSign = md5($url . '|' . $timestamp . '|' . $key);
        if ($localSign !== $vodSign) {
            abort(403);
        }

        // 事件类型参考:https://help.aliyun.com/document_detail/55627.html
        $eventType = $request->input('EventType');
        $params = $request->all();

        event(new AliVodCallbackEvent($eventType, $timestamp, $params));

        return 'success';
    }
}

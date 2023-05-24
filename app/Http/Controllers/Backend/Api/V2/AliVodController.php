<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Backend\Api\V2;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Constant\RuntimeConstant as RC;
use App\Meedu\ServiceV2\Services\AliVodServiceInterface;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;
use App\Meedu\ServiceV2\Services\SettingServiceInterface;
use App\Meedu\ServiceV2\Services\RuntimeStatusServiceInterface;

class AliVodController extends BaseController
{
    public function check(
        ConfigServiceInterface        $cService,
        RuntimeStatusServiceInterface $rsService,
        SettingServiceInterface       $settingService,
        AliVodServiceInterface        $avService
    ) {
        $config = $cService->getAliVodConfig();

        $baseConfigOk = $config['access_key_id'] && $config['access_key_secret'];

        $runtime = array_column($rsService->aliVodStatus(), null, 'name');

        // 基础配置的状态
        if (!$runtime[RC::ALI_VOD_SECRET]['status'] && $baseConfigOk) {
            $rsService->setAliVodSecret(true);
        }

        // 播放域名的配置
        if (!$runtime[RC::ALI_VOD_DOMAIN]['status'] && $config['domain']) {
            $rsService->setAliVodDomain($config['domain']);
        }

        if ($baseConfigOk) {
            //event的创建
            if (!$runtime[RC::ALI_VOD_EVENT]['status']) {
                $callbackKey = Str::random(32);
                $pushUrl = route('ali.vod.callback');

                try {
                    $avService->saveEventConfig($callbackKey, $pushUrl, $config['app_id']);
                    $settingService->saveAliVodCallbackKey($callbackKey);
                    $rsService->setAliVodEvent($pushUrl);
                } catch (\Exception $e) {
                    $msg = __('事件回调配置失败,错误信息：:msg', ['msg' => $e->getMessage()]);
                    Log::error(__METHOD__ . '|' . $msg);
                    return $this->error($msg);
                }
            }

            // 多清晰度任务的创建
            if (!$runtime[RC::ALI_VOD_TRANSCODE]['status']) {
                try {
                    if (!$avService->isTranscodeSimpleTaskExists($config['app_id'])) {
                        $avService->transcodeTemplateStore($config['app_id']);
                    }
                    $rsService->setAliVodTranscodeSimpleTask(true);
                } catch (\Exception $e) {
                    $msg = __('转码任务创建失败,错误信息：:msg', ['msg' => $e->getMessage()]);
                    Log::error(__METHOD__ . '|' . $msg);
                    return $this->error($msg);
                }
            }
        }

        $runtime = $rsService->aliVodStatus();

        return $this->successData($runtime);
    }

    public function transcodeConfig(ConfigServiceInterface $configService, AliVodServiceInterface $avServ)
    {
        $config = $configService->getAliVodConfig();

        $templates = $avServ->transcodeTemplates($config['app_id']);

        $data = [];
        foreach ($templates as $templateItem) {
            $name = $templateItem['Name'];
            if (!Str::startsWith($name, 'MeEdu')) {
                continue;
            }
            $data[] = $templateItem;
        }

        return $this->successData($data);
    }

    public function transcodeSubmit(Request $request, ConfigServiceInterface $configService, AliVodServiceInterface $avServ)
    {
        $fileId = $request->input('file_id');
        $templateName = $request->input('template_name');
        if (!$fileId || !$templateName) {
            return $this->error(__('参数错误'));
        }

        $config = $configService->getAliVodConfig();

        $avServ->transcodeSubmit($config['app_id'], $fileId, $templateName);

        return $this->success();
    }

    public function transcodeDestroy(Request $request, AliVodServiceInterface $avServ)
    {
        $fileId = $request->input('file_id');
        if (!$fileId) {
            return $this->error(__('参数错误'));
        }

        $avServ->transcodeDestroy($fileId);

        return $this->success();
    }
}

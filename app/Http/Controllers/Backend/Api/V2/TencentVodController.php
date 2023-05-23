<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Backend\Api\V2;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Constant\TencentConstant;
use Illuminate\Support\Facades\Log;
use App\Constant\RuntimeConstant as RC;
use App\Meedu\ServiceV2\Models\TencentVideoTranscode;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;
use App\Meedu\ServiceV2\Services\SettingServiceInterface;
use App\Meedu\ServiceV2\Services\TencentVodServiceInterface;
use App\Meedu\ServiceV2\Services\RuntimeStatusServiceInterface;

class TencentVodController extends BaseController
{
    public function check(
        ConfigServiceInterface        $cService,
        RuntimeStatusServiceInterface $rsService,
        SettingServiceInterface       $settingService,
        TencentVodServiceInterface    $tvService
    ) {
        $config = $cService->getTencentVodConfig();
        $playKey = $cService->getTencentVodPlayKey();

        $baseConfigOk = $config['secret_id'] && $config['secret_key'];

        $runtime = array_column($rsService->tencentVodStatus(), null, 'name');

        // 基础配置的状态
        if (!$runtime[RC::TENCENT_VOD_SECRET]['status'] && $baseConfigOk) {
            $rsService->setTencentVodSecret(true);
        }

        // 子应用的创建
        if (!$runtime[RC::TENCENT_VOD_APP]['status'] && $config['app_id']) {
            $rsService->setTencentVodApp($config['app_id']);
        }

        // 播放域名的配置
        if (!$runtime[RC::TENCENT_VOD_DOMAIN]['status'] && $config['domain']) {
            $rsService->setTencentVodDomain($config['domain']);
        }

        // 播放key的配置
        if (!$runtime[RC::TENCENT_VOD_DOMAIN_KEY]['status'] && $playKey) {
            $rsService->setTencentVodDomainKey(true);
        }

        if ($baseConfigOk && $config['app_id']) {
            if (!$runtime[RC::TENCENT_VOD_EVENT]['status']) {  // event的创建
                $callbackKey = Str::random(32);
                $pushUrl = route('tencent.vod.callback', ['key' => $callbackKey]);
                try {
                    $tvService->eventSet($config['app_id'], $pushUrl);
                    $settingService->saveTencentVodCallbackKey($callbackKey);
                    $rsService->setTencentVodEvent($pushUrl);
                } catch (\Exception $e) {
                    $msg = __('事件回调配置失败,错误信息：:msg', ['msg' => $e->getMessage()]);
                    Log::error(__METHOD__ . '|' . $msg);
                    return $this->error($msg);
                }
            }

            // 多清晰度任务的创建
            if (!$runtime[RC::TENCENT_VOD_TRANSCODE_TASK_SIMPLE]['status']) {
                try {
                    if (!$tvService->isTranscodeSimpleTaskExists($config['app_id'])) {
                        $tvService->transcodeSimpleTaskSet($config['app_id']);
                    }
                    $rsService->setTencentVodTranscodeSimpleTask(true);
                } catch (\Exception $e) {
                    $msg = __('转码任务创建失败,错误信息：:msg', ['msg' => $e->getMessage()]);
                    Log::error(__METHOD__ . '|' . $msg);
                    return $this->error($msg);
                }
            }
        }

        $runtime = $rsService->tencentVodStatus();

        return $this->successData($runtime);
    }

    public function apps(TencentVodServiceInterface $tvService)
    {
        return $this->successData($tvService->apps());
    }

    public function appConfirm(
        Request                       $request,
        TencentVodServiceInterface    $tvService,
        SettingServiceInterface       $settingService,
        RuntimeStatusServiceInterface $rsService,
        ConfigServiceInterface        $cService
    ) {
        $config = $cService->getTencentVodConfig();
        if ($config['app_id']) {
            return $this->error(__('已配置'));
        }

        $name = $request->input('name');
        $subAppId = $request->input('sub_app_id');
        if (!$subAppId) {
            if (!$name) {
                return $this->error(__('请输入应用名'));
            }
            $subAppId = $tvService->storeApp($name);
        }

        $settingService->saveTencentVodAppId($subAppId);

        $rsService->setTencentVodApp($subAppId);

        return $this->success();
    }

    public function domains(TencentVodServiceInterface $tvService)
    {
        return $this->successData($tvService->domains());
    }

    public function domainSwitch(
        Request                       $request,
        TencentVodServiceInterface    $tvService,
        SettingServiceInterface       $settingService,
        RuntimeStatusServiceInterface $rsService,
        ConfigServiceInterface        $cService
    ) {
        $domain = $request->input('domain');
        if (!$domain) {
            return $this->error(__('参数错误'));
        }

        $config = $cService->getTencentVodConfig();
        if ($config['domain'] === $domain) {
            return $this->error(__('请勿重复配置'));
        }

        try {
            $playKey = Str::random(32);

            $tvService->domainKeySet($domain, $playKey);

            $settingService->saveTencentVodDomainAndKey($domain, $playKey);

            $rsService->setTencentVodDomain($domain);

            return $this->success();
        } catch (\Exception $e) {
            $msg = __('播放域名配置失败,错误信息：:msg', ['msg' => $e->getMessage()]);
            Log::error(__METHOD__ . '|' . $msg);
            return $this->error($msg);
        }
    }

    public function domainKeyReset(
        TencentVodServiceInterface    $tvService,
        SettingServiceInterface       $settingService,
        RuntimeStatusServiceInterface $rsService
    ) {
        try {
            $playKey = Str::random(32);

            $tvService->defaultDomainKeySet($playKey);

            $settingService->saveTencentVodDomainKey($playKey);

            $rsService->setTencentVodDomainKey(true);

            return $this->success();
        } catch (\Exception $e) {
            $msg = __('播放域名配置失败,错误信息：:msg', ['msg' => $e->getMessage()]);
            Log::error(__METHOD__ . '|' . $msg);
            return $this->error($msg);
        }
    }

    public function transcodeDestroy(Request $request, TencentVodServiceInterface $tvService)
    {
        $fileIds = $request->input('file_ids');
        if (!$fileIds || !is_array($fileIds)) {
            return $this->error(__('参数错误'));
        }

        try {
            $tvService->deleteVideo($fileIds, [TencentConstant::VOD_DELETE_PART_TRANSCODE, TencentConstant::VOD_DELETE_PART_ADAPTIVE]);
            TencentVideoTranscode::query()->whereIn('file_id', $fileIds)->delete();
            return $this->success();
        } catch (\Exception $e) {
            $msg = __('腾讯云视频删除失败，错误信息：:msg', ['msg' => $e->getMessage()]);
            return $this->error($msg);
        }
    }

    public function transcodeSubmit(Request $request, TencentVodServiceInterface $tvService)
    {
        $fileIds = $request->input('file_ids');
        $templateName = $request->input('template_name');
        if (!$fileIds || !is_array($fileIds) || !$templateName) {
            return $this->error(__('参数错误'));
        }

        if (!in_array($templateName, TencentConstant::VOD_TRANSCODE_NAMES)) {
            return $this->error(__('参数错误'));
        }

        if (count($fileIds) > 20) {
            return $this->error(__('参数错误'));
        }

        $records = TencentVideoTranscode::query()->whereIn('file_id', $fileIds)->where('template_name', $templateName)->get()->keyBy('file_id');

        $suc = [];
        $fail = [];

        foreach ($fileIds as $fileIdItem) {
            if (isset($records[$fileIdItem])) {
                $fail = [
                    'file_id' => $fileIdItem,
                    'msg' => __('请勿重复提交转码。最近提交时间：:date', ['date' => $records[$fileIdItem]['created_at']]),
                ];
                continue;
            }

            try {
                $tvService->transcodeSubmit($fileIdItem, $templateName);
                $suc[] = [
                    'file_id' => $fileIdItem,
                    'msg' => 'ok',
                ];
                // 写入到数据库
                TencentVideoTranscode::create([
                    'file_id' => $fileIdItem,
                    'template_name' => $templateName,
                ]);
            } catch (\Exception $e) {
                $msg = __('提交转码出错，错误信息：:msg', ['msg' => $e->getMessage()]);
                Log::error(__METHOD__ . '|' . $msg, compact('fileIdItem', 'templateName'));
                $fail[] = [
                    'file_id' => $fileIdItem,
                    'msg' => $msg,
                ];
            }
        }

        return $this->successData([
            'suc' => $suc,
            'fail' => $fail,
        ]);
    }
}

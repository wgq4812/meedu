<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Backend\Api\V1;

use Illuminate\Http\Request;
use App\Models\AdministratorLog;
use App\Constant\FrontendConstant;
use Illuminate\Support\Facades\DB;
use App\Services\Course\Models\MediaVideo;
use App\Meedu\ServiceV2\Services\AliVodServiceInterface;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;
use App\Meedu\ServiceV2\Services\TencentVodServiceInterface;

class MediaVideoController extends BaseController
{
    public function index(Request $request, AliVodServiceInterface $avService, TencentVodServiceInterface $tvService)
    {
        $keywords = $request->input('keywords');
        $isOpen = (int)$request->input('is_open');

        $sort = strtolower($request->input('sort', 'id'));
        $order = strtolower($request->input('order', 'desc'));
        if (!in_array($sort, ['id', 'duration', 'size', 'created_at']) || !in_array($order, ['desc', 'asc'])) {
            return $this->error(__('排序参数错误'));
        }

        $videos = MediaVideo::query()
            ->select([
                'id', 'title', 'thumb', 'duration', 'size', 'storage_driver', 'storage_file_id',
                'transcode_status', 'ref_count', 'created_at', 'updated_at',
            ])
            ->when($keywords, function ($query) use ($keywords) {
                $query->where('title', 'like', '%' . $keywords . '%');
            })
            ->when(in_array($isOpen, [0, 1]), function ($query) use ($isOpen) {
                $query->where('is_open', $isOpen);
            })
            ->orderByDesc('id')
            ->paginate($request->input('size', 10));

        AdministratorLog::storeLog(
            AdministratorLog::MODULE_ADMIN_MEDIA_VIDEO,
            AdministratorLog::OPT_VIEW,
            compact('keywords', 'isOpen')
        );

        $aliFileIds = [];
        $aliTranscodeData = [];

        $tencentFileIds = [];
        $tencentTranscodeData = [];

        $data = $videos->items();
        $total = $videos->total();

        foreach ($data as $tmpItem) {
            if ($tmpItem['storage_driver'] === FrontendConstant::VOD_SERVICE_ALIYUN) {
                $aliFileIds[] = $tmpItem['storage_file_id'];
            } elseif ($tmpItem['storage_driver'] === FrontendConstant::VOD_SERVICE_TENCENT) {
                $tencentFileIds[] = $tmpItem['storage_file_id'];
            }
        }

        if ($aliFileIds) {
            $aliTranscodeData = collect($avService->chunks($aliFileIds))->groupBy('file_id')->toArray();
        }
        if ($tencentFileIds) {
            $tencentTranscodeData = collect($tvService->chunks($tencentFileIds))->groupBy('file_id')->toArray();
        }

        return $this->successData([
            'data' => $data,
            'total' => $total,
            'ali_transcode' => $aliTranscodeData,
            'tencent_transcode' => $tencentTranscodeData,
        ]);
    }

    public function destroy(Request $request, AliVodServiceInterface $avService, TencentVodServiceInterface $tvService)
    {
        $ids = $request->input('ids');
        if (!$ids || !is_array($ids)) {
            return $this->error(__('请选择需要删除的视频'));
        }

        AdministratorLog::storeLog(
            AdministratorLog::MODULE_ADMIN_MEDIA_VIDEO,
            AdministratorLog::OPT_DESTROY,
            compact('ids')
        );

        $videos = MediaVideo::query()->whereIn('id', $ids)->select(['id', 'storage_driver', 'storage_file_id'])->get()->toArray();
        if (!$videos) {
            return $this->error(__('数据为空'));
        }

        $aliFileIds = [];
        $tencentFileIds = [];
        foreach ($videos as $videoItem) {
            if ($videoItem['storage_driver'] === FrontendConstant::VOD_SERVICE_ALIYUN) {
                $aliFileIds[] = $videoItem['storage_file_id'];
            } elseif ($videoItem['storage_driver'] === FrontendConstant::VOD_SERVICE_TENCENT) {
                $tencentFileIds[] = $videoItem['storage_file_id'];
            }
        }

        DB::transaction(function () use ($ids, $aliFileIds, $tencentFileIds, $avService, $tvService) {
            MediaVideo::query()->whereIn('id', $ids)->delete();

            if ($aliFileIds) {
                $avService->destroyMulti($aliFileIds);
            }
            if ($tencentFileIds) {
                $tvService->destroyMulti($tencentFileIds);
            }
        });

        return $this->successData();
    }

    public function transcodeConfig(ConfigServiceInterface $configService, AliVodServiceInterface $avServ, TencentVodServiceInterface $tvService)
    {
        $config = $configService->getAliVodConfig();

        AdministratorLog::storeLog(
            AdministratorLog::MODULE_ADMIN_MEDIA_VIDEO,
            AdministratorLog::OPT_VIEW,
            []
        );

        return $this->successData([
            'ali_templates' => $avServ->defaultTranscodeTemplates($config['app_id']),
            'tencent_templates' => $tvService->transcodeTemplates(),
        ]);
    }

    public function transcodeDestroy(Request $request, AliVodServiceInterface $avServ, TencentVodServiceInterface $tvService)
    {
        $fileId = $request->input('file_id');
        $service = $request->input('service');
        if (!$fileId) {
            return $this->error(__('参数错误'));
        }

        AdministratorLog::storeLog(
            AdministratorLog::MODULE_TENCENT_VOD,
            AdministratorLog::OPT_DESTROY,
            compact('fileId', 'service')
        );

        if ($service === FrontendConstant::VOD_SERVICE_TENCENT) {
            $tvService->deleteVideo([$fileId]);
        } elseif ($service === FrontendConstant::VOD_SERVICE_ALIYUN) {
            $avServ->transcodeDestroy($fileId);
        }

        return $this->success();
    }

    public function transcodeSubmit(Request $request, ConfigServiceInterface $configService, AliVodServiceInterface $avServ, TencentVodServiceInterface $tvService)
    {
        $fileId = $request->input('file_id');
        $tempName = $request->input('template_name');
        $service = $request->input('service');
        if (!$fileId || !$tempName || !$service) {
            return $this->error(__('参数错误'));
        }

        AdministratorLog::storeLog(
            AdministratorLog::MODULE_ALI_VOD,
            AdministratorLog::OPT_STORE,
            compact('fileId', 'tempName', 'service')
        );

        if ($service === FrontendConstant::VOD_SERVICE_TENCENT) {
            $tvService->transcodeSubmit($fileId, $tempName);
        } elseif ($service === FrontendConstant::VOD_SERVICE_ALIYUN) {
            $tempId = $request->input('template_id');
            if (!$tempId) {
                return $this->error(__('参数错误'));
            }
            $config = $configService->getAliVodConfig();
            $avServ->transcodeSubmit($config['app_id'], $fileId, $tempName, $tempId);
        }

        return $this->success();
    }
}

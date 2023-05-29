<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Backend\Api\V1;

use Illuminate\Http\Request;
use App\Models\AdministratorLog;
use App\Meedu\Tencent\Vod as TencentVod;
use App\Meedu\ServiceV2\Services\AliVodServiceInterface;

class VideoUploadController extends BaseController
{
    public function tencentToken(TencentVod $vod)
    {
        $signature = $vod->getUploadSignature();

        AdministratorLog::storeLog(
            AdministratorLog::MODULE_ADMIN_MEDIA_VIDEO,
            AdministratorLog::OPT_VIEW,
            []
        );

        return $this->successData(compact('signature'));
    }

    public function aliyunCreateVideoToken(Request $request, AliVodServiceInterface $avService)
    {
        $title = $request->input('title');
        $filename = $request->input('filename');
        if (!$title || !$filename) {
            return $this->error(__('参数错误'));
        }

        AdministratorLog::storeLog(
            AdministratorLog::MODULE_ADMIN_MEDIA_VIDEO,
            AdministratorLog::OPT_VIEW,
            compact('title', 'filename')
        );

        $data = $avService->createUploadToken($filename, $title);

        return $this->successData($data);
    }

    public function aliyunRefreshVideoToken(Request $request, AliVodServiceInterface $avService)
    {
        $videoId = $request->input('video_id');
        if (!$videoId) {
            return $this->error(__('参数错误'));
        }

        AdministratorLog::storeLog(
            AdministratorLog::MODULE_ADMIN_MEDIA_VIDEO,
            AdministratorLog::OPT_VIEW,
            compact('videoId')
        );

        $data = $avService->createUploadRefreshToken($videoId);

        return $this->successData($data);
    }
}

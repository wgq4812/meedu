<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Bus;

use App\Meedu\Ali\Vod;
use App\Constant\AliConstant;
use App\Constant\TencentConstant;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceException;
use App\Meedu\ServiceV2\Services\AliVodServiceInterface;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;
use App\Meedu\ServiceV2\Services\TencentVodServiceInterface;

class VodPlayBus
{
    private $configService;

    public function __construct(ConfigServiceInterface $configService)
    {
        $this->configService = $configService;
    }

    /**
     * @param array $video
     * @param int $trySeconds
     * @return array
     * @throws ServiceException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getPlayInfo(array $video, int $trySeconds): array
    {
        if ($video['aliyun_video_id']) {//阿里云
            $service = 'ali';
            $data = $this->getAliPlayInfo($video['aliyun_video_id'], $trySeconds);
        } elseif ($video['tencent_video_id']) {//腾讯云
            $service = 'tencent';
            $data = $this->getTencentPlayInfo($video['tencent_video_id'], $trySeconds);
        } else {
            $service = 'url';
            $data = $this->getUrlPlayInfo($video, $trySeconds);
        }

        return compact('service', 'data');
    }

    /**
     * @param string $fileId
     * @param int $trySeconds
     * @return array
     * @throws ServiceException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getAliPlayInfo(string $fileId, int $trySeconds): array
    {
        /**
         * @var Vod $vod
         */
        $vod = app()->make(Vod::class);
        /**
         * @var AliVodServiceInterface $avServ
         */
        $avServ = app()->make(AliVodServiceInterface::class);

        // 配置读取
        $config = $this->configService->getAliVodConfig();
        if (!$config['domain']) {
            throw new ServiceException(__('未配置播放域名'));
        }

        $resultType = 'Single';

        // 转码记录读取
        $transcodeRecords = array_column($avServ->getTranscodeRecords($fileId), 'template_name');

        $encryptType = 'Unencrypted';//默认返回非加密
        if (in_array(AliConstant::VOD_TRANSCODE_HLS_PRIVATE, $transcodeRecords)) {//返回私有加密的播放地址
            $encryptType = 'AliyunVoDEncryption';
            $resultType = 'Multiple';
        } elseif (in_array(AliConstant::VOD_TRANSCODE_HLS_SIMPLE, $transcodeRecords)) {//返回hls标准加密的播放地址
            $encryptType = 'HLSEncryption';
        }

        // 播放地址返回格式配置
        $formats = 'mp4';//默认返回mp4格式
        if ($transcodeRecords) {
            $formats = 'm3u8';
        }

        $playConfig = [
            'PlayDomain' => $config['domain'],//播放的域名
            'EncryptType' => $encryptType,
        ];
        $trySeconds > 0 && $playConfig['PreviewTime'] = $trySeconds;

        $params = [
            'Formats' => $formats,
            'AuthTimeout' => 3600,//一个小时有效期
            'OutputType' => 'cdn',//返回cdn加速地址
            'ResultType' => $resultType,
            'PlayConfig' => json_encode($playConfig),
        ];

        $playList = $vod->playInfo($fileId, $params);
        if ($playList === false) {
            Log::error(__METHOD__ . '|无法获取视频播放地址', ['err' => $vod->getErrMsg(), 'file_id' => $fileId]);
            throw new ServiceException(__('无法获取播放地址'));
        }

        $data = [];
        foreach ($playList['PlayInfo'] as $infoItem) {
            if ($infoItem['Status'] !== 'Normal') {
                continue;
            }
            $data[] = [
                'url' => $infoItem['PlayURL'],
                'format' => $infoItem['Format'],
                'name' => $infoItem['Definition'],
                'duration' => (int)$infoItem['Duration'],
            ];
        }

        return [
            'data' => $data,
            'encrypt_type' => $encryptType,
        ];
    }

    /**
     * @param string $fileId
     * @param int $trySeconds
     * @return array
     * @throws ServiceException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getTencentPlayInfo(string $fileId, int $trySeconds): array
    {
        /**
         * @var \App\Meedu\Tencent\Vod $vod
         */
        $vod = app()->make(\App\Meedu\Tencent\Vod::class);
        /**
         * @var TencentVodServiceInterface $avServ
         */
        $tvServ = app()->make(TencentVodServiceInterface::class);

        $config = $this->configService->getTencentVodConfig();
        if (!$config['domain']) {
            throw new ServiceException(__('未配置播放域名'));
        }

        // 转码记录读取
        $transcodeRecords = array_column($tvServ->getTranscodeRecords([$fileId]), 'template_name');

        $mode = '';
        if (in_array(TencentConstant::VOD_TRANSCODE_ADAPTIVE, $transcodeRecords)) {
            $mode = TencentConstant::VOD_TRANSCODE_ADAPTIVE;
        } elseif (in_array(TencentConstant::VOD_TRANSCODE_SIMPLE_TASK, $transcodeRecords)) {
            $mode = TencentConstant::VOD_TRANSCODE_SIMPLE_TASK;
        }

        return [
            'player_sign' => [
                'fileId' => $fileId,
                'sign' => $vod->getPlayerSign($fileId, $trySeconds, $mode),
                'app_id' => (int)$config['app_id'],
            ],
            'urls' => $vod->getPlayUrls($fileId, $trySeconds, $mode),
            'mode' => $mode,
        ];
    }

    protected function getUrlPlayInfo(array $video, int $trySeconds): array
    {
        return [
            [
                'url' => $video['url'],
                'format' => pathinfo($video['url'], PATHINFO_EXTENSION),
                'name' => 'Default',
                'duration' => 0,
            ],
        ];
    }
}

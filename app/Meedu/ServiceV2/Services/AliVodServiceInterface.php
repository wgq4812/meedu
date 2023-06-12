<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface AliVodServiceInterface
{
    /**
     * 配置回调事件
     * @param string $callbackKey
     * @param string $callbackUrl
     * @param string $appId
     * @return mixed
     */
    public function saveEventConfig(string $callbackKey, string $callbackUrl, string $appId);

    /**
     * 获取转码模板列表
     * @param string $appId
     * @return array
     */
    public function transcodeTemplates(string $appId): array;

    /**
     * 获取MeEdu内置的转码模板
     * @param string $appId
     * @return array
     */
    public function defaultTranscodeTemplates(string $appId): array;

    /**
     * 创建转码模板
     * @param string $appId
     * @param bool $isEncrypt
     * @return string
     */
    public function transcodeTemplateStore(string $appId, bool $isEncrypt): string;

    /**
     * 获取阿里云的点播播放域名
     * @param int $page
     * @param int $size
     * @return array
     */
    public function domains(int $page = 1, int $size = 50): array;

    /**
     * 提交转码并创建数据库记录
     * @param string $appId
     * @param string $fileId
     * @param string $tempName
     * @param string $tempId
     * @return void
     */
    public function transcodeSubmit(string $appId, string $fileId, string $tempName, string $tempId): void;

    /**
     * 删除videoId的转码文件和数据库记录
     * @param string $fileId
     * @return void
     */
    public function transcodeDestroy(string $fileId): void;

    /**
     * 获取单个file的转码记录
     * @param string $fileId
     * @return array
     */
    public function getTranscodeRecords(string $fileId): array;

    /**
     * 批量获取fileIds的转码记录
     * @param array $fileIds
     * @return array
     */
    public function chunks(array $fileIds): array;

    /**
     * 批量删除fileIds
     * @param array $fileIds
     * @return void
     */
    public function destroyMulti(array $fileIds): void;

    /**
     * 创建视频上传token
     * @param string $fileName
     * @param string $title
     * @return array
     */
    public function createUploadToken(string $fileName, string $title): array;

    /**
     * 创建视频上传刷新token
     * @param string $fileId
     * @return array
     */
    public function createUploadRefreshToken(string $fileId): array;

    /**
     * 获取hls解密密钥
     * @param string $key
     * @return string
     */
    public function decryptKMSDataKey(string $key): string;

    /**
     * 获取视频播放url
     * @param string $fileId
     * @param array $extra
     * @return array
     */
    public function playInfo(string $fileId, array $extra): array;
}
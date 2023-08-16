<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface TencentVodServiceInterface
{
    /**
     * 指定域名的播放key配置
     * @param string $domain
     * @param string $key
     * @return mixed
     */
    public function domainKeySet(string $domain, string $key);

    /**
     * 默认点播域名(已配置到系统中的点播域名)的播放key配置
     * @param string $key
     * @return mixed
     */
    public function defaultDomainKeySet(string $key);

    /**
     * 点播域名列表
     * @return array
     */
    public function domains(): array;

    /**
     * 子应用列表
     * @return array
     */
    public function apps(): array;

    /**
     * 创建子应用
     * @param string $name
     * @return mixed
     */
    public function storeApp(string $name);

    /**
     * 回调事件配置
     * @param string $subAppId
     * @param string $url
     * @return mixed
     */
    public function eventSet(string $subAppId, string $url);

    /**
     * 判断是否创建了MeEdu默认的视频处理任务流
     * @param string $subAppId
     * @return bool
     */
    public function isTranscodeSimpleTaskExists(string $subAppId): bool;

    /**
     * 创建视频处理任务流
     * @param string $subAppId
     * @return mixed
     */
    public function transcodeSimpleTaskSet(string $subAppId);

    /**
     * 批量删除fileIds
     * @param array $fileIds
     * @return mixed
     */
    public function deleteVideo(array $fileIds);

    /**
     * 提交转码并创建数据库记录
     * @param string $fileId
     * @param string $templateName
     * @return void
     */
    public function transcodeSubmit(string $fileId, string $templateName): void;

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
     * 获取视频上传sign
     * @return string
     */
    public function getUploadSignature(): string;

    /**
     * 获取签名后的视频url
     * @param string $url
     * @param $trySeconds
     * @return string
     */
    public function getSignUrl(string $url, $trySeconds): string;

    /**
     * 获取播放器签名
     * @param string $fileId
     * @param int $trySeconds
     * @param string $mode
     * @return string
     */
    public function getPlayerSign(string $fileId, int $trySeconds, string $mode): string;

    /**
     * 获取播放URL
     * @param string $fileId
     * @param int $trySeconds
     * @param string $mode
     * @return array
     */
    public function getPlayUrls(string $fileId, int $trySeconds, string $mode): array;

    /**
     * @return array
     */
    public function transcodeTemplates(): array;
}

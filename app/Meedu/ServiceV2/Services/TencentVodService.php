<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use Carbon\Carbon;
use App\Meedu\Tencent\Vod;
use App\Constant\TencentConstant;
use App\Exceptions\ServiceException;
use App\Meedu\ServiceV2\Dao\VodDaoInterface;
use App\Meedu\Tencent\Sub\RefererAuthPolicy;
use App\Meedu\Tencent\Sub\UrlSignatureAuthPolicy;

class TencentVodService implements TencentVodServiceInterface
{
    private $configService;

    private $vod;

    private $vodDao;

    public function __construct(ConfigServiceInterface $configService, Vod $vod, VodDaoInterface $vodDao)
    {
        $this->configService = $configService;
        $this->vod = $vod;
        $this->vodDao = $vodDao;
    }

    /**
     * 默认播放域名重置
     * @param string $key
     * @return void
     * @throws ServiceException
     */
    public function defaultDomainKeySet(string $key)
    {
        $config = $this->configService->getTencentVodConfig();
        if (!$config['domain']) {
            throw new ServiceException(__('未配置播放域名'));
        }
        $this->domainKeySet($config['domain'], $key);
    }

    /**
     * 播放域名key重置
     * @param string $domain
     * @param string $key
     * @return void
     */
    public function domainKeySet(string $domain, string $key)
    {
        $config = $this->configService->getTencentVodConfig();

        $refererAuthPolicy = new RefererAuthPolicy();
        $refererAuthPolicy->setStatus('Disabled');

        $urlSignatureAuthPolicy = new UrlSignatureAuthPolicy();
        $urlSignatureAuthPolicy->setStatus('Enabled');
        $urlSignatureAuthPolicy->setEncryptedKey($key);

        $this->vod->domainConfigPut($config['app_id'], $domain, $refererAuthPolicy, $urlSignatureAuthPolicy);
    }

    /**
     * @return array
     */
    public function domains(): array
    {
        $config = $this->configService->getTencentVodConfig();
        return $this->vod->domainList($config['app_id']);
    }

    /**
     * @return array
     */
    public function apps(): array
    {
        return $this->vod->appList();
    }

    /**
     * @param string $name
     * @return int
     */
    public function storeApp(string $name)
    {
        return $this->vod->appStore($name);
    }

    /**
     * @param string $subAppId
     * @param string $url
     * @return void
     */
    public function eventSet(string $subAppId, string $url)
    {
        $this->vod->eventConfigPut($subAppId, $url);
    }

    /**
     * @param string $subAppId
     * @return bool
     */
    public function isTranscodeSimpleTaskExists(string $subAppId): bool
    {
        $data = $this->vod->procedureTemplatesList($subAppId);
        $names = array_column($data['data'], 'name');
        return in_array(TencentConstant::VOD_TRANSCODE_SIMPLE_TASK, $names);
    }

    /**
     * 创建默认的无加密转码任务流[MeEduSimple]
     * @param string $subAppId
     * @return void
     */
    public function transcodeSimpleTaskSet(string $subAppId)
    {
        $this->vod->procedureTemplateStoreSimple($subAppId);
    }

    /**
     * 删除视频文件
     * @param array $fileIds
     * @return void
     */
    public function deleteVideo(array $fileIds)
    {
        $this->vod->deleteVideos($fileIds, [
            TencentConstant::VOD_DELETE_PART_TRANSCODE,
            TencentConstant::VOD_DELETE_PART_ADAPTIVE,
        ]);
        $this->vodDao->clearTencentTranscodeRecords($fileIds);
    }

    /**
     * @param string $fileId
     * @param string $templateName
     * @return void
     * @throws ServiceException
     */
    public function transcodeSubmit(string $fileId, string $templateName): void
    {
        // 重复提交判断
        $record = $this->vodDao->findTencentTranscodeRecord($fileId, $templateName);
        if ($record) {
            throw new ServiceException(__('请勿重复提交转码。最近提交时间：:date', ['date' => Carbon::parse($record['created_at'])->format('Y-m-d H:i:s')]));
        }

        $templates = $this->vod->defaultProcedureTemplatesList();
        if (!in_array($templateName, array_column($templates['data'], 'name'))) {
            throw new ServiceException(__('转码模板不存在'));
        }

        // 提交转码
        $this->vod->transcodeSubmit($fileId, $templateName);
        // 状态记录
        $this->vodDao->storeTencentTranscodeRecord($fileId, $templateName);
    }

    public function chunks(array $fileIds): array
    {
        return $this->vodDao->getTencentTranscodeRecords($fileIds, '');
    }
}

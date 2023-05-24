<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Meedu\Ali\Vod;
use App\Exceptions\ServiceException;
use App\Meedu\ServiceV2\Dao\VodDaoInterface;

class AliVodService implements AliVodServiceInterface
{
    private $vod;

    private $vodDao;

    public function __construct(Vod $vod, VodDaoInterface $vodDao)
    {
        $this->vod = $vod;
        $this->vodDao = $vodDao;
    }

    /**
     * @param string $callbackKey
     * @param string $callbackUrl
     * @param string $appId
     * @return void
     * @throws ServiceException
     */
    public function saveEventConfig(string $callbackKey, string $callbackUrl, string $appId)
    {
        $result = $this->vod->eventStore($appId, $callbackUrl, $callbackKey);
        if (!$result) {
            throw new ServiceException($this->vod->getErrMsg());
        }
    }

    /**
     * 创建MeEdu默认的转码模板
     * @param string $appId
     * @return void
     * @throws ServiceException
     */
    public function transcodeTemplateStore(string $appId)
    {
        $result = $this->vod->templateStore($appId);
        if (!$result) {
            throw new ServiceException($this->vod->getErrMsg());
        }
    }

    /**
     * 判断MeEdu默认的转码模板是否存在
     * @param string $appId
     * @return bool
     * @throws ServiceException
     */
    public function isTranscodeSimpleTaskExists(string $appId): bool
    {
        $result = $this->vod->templates($appId);
        if ($result === false) {
            throw new ServiceException('无法获取转码模板列表');
        }
        $names = array_column($result, 'Name');
        return in_array('MeEduSimple', $names);
    }

    /**
     * 获取点播域名列表
     * @param int $page
     * @param int $size
     * @return array
     * @throws ServiceException
     */
    public function domains(int $page = 1, int $size = 50): array
    {
        $data = $this->vod->domains($page, $size);
        if ($data === false) {
            throw new ServiceException($this->vod->getErrMsg());
        }
    }

    /**
     * @param string $appId
     * @param string $fileId
     * @param string $templateName
     * @return void
     * @throws ServiceException
     */
    public function transcodeSubmit(string $appId, string $fileId, string $templateName): void
    {
        // 读取转码模板列表
        $templates = $this->vod->templates($appId);
        if ($templates === false) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        $templates = array_column($templates, null, 'Name');
        if (!isset($templates[$templateName])) {
            throw new ServiceException('转码模板不存在');
        }
        $templateId = $templates[$templateName]['TranscodeTemplateGroupId'];

        // 删除已有的转码记录，不管是否存在
        $this->vod->deleteStream($fileId);
        $this->vodDao->cleanAliTranscodeRecords($fileId);

        // 提交转码
        $result = $this->vod->transcodeSubmit($fileId, $templateId);
        if ($result === false) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        $this->vodDao->storeTencentTranscodeRecord($fileId, $templateName);
    }

    /**
     * @param string $videoId
     * @return void
     * @throws ServiceException
     */
    public function transcodeDestroy(string $videoId): void
    {
        $result = $this->vod->deleteStream($videoId);
        if (!$result) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        //清空本地转码记录
        $this->vodDao->cleanAliTranscodeRecords($videoId);
    }

    /**
     * @param string $appId
     * @return array
     * @throws ServiceException
     */
    public function transcodeTemplates(string $appId): array
    {
        $result = $this->vod->templates($appId);
        if ($result === false) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        return $result;
    }
}

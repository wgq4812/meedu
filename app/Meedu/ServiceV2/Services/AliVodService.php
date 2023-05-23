<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Meedu\Ali\Vod;
use App\Exceptions\ServiceException;

class AliVodService implements AliVodServiceInterface
{
    private $vod;

    public function __construct(Vod $vod)
    {
        $this->vod = $vod;
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
}

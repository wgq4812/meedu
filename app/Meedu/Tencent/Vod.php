<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Tencent;

use Illuminate\Support\Facades\Log;
use App\Meedu\Tencent\Sub\RefererAuthPolicy;
use App\Services\Base\Services\ConfigService;
use App\Meedu\Tencent\Sub\UrlSignatureAuthPolicy;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;
use TencentCloud\Vod\V20180717\Models\DeleteMediaRequest;
use TencentCloud\Vod\V20180717\Models\TranscodeTaskInput;
use TencentCloud\Vod\V20180717\Models\CreateSubAppIdRequest;
use TencentCloud\Vod\V20180717\Models\MediaProcessTaskInput;
use TencentCloud\Vod\V20180717\Models\CreateVodDomainRequest;
use TencentCloud\Vod\V20180717\Models\DescribeSubAppIdsRequest;
use TencentCloud\Vod\V20180717\Models\ModifyEventConfigRequest;
use TencentCloud\Vod\V20180717\Models\DescribeVodDomainsRequest;
use TencentCloud\Vod\V20180717\Models\DescribeEventConfigRequest;
use TencentCloud\Vod\V20180717\Models\ModifyVodDomainConfigRequest;
use TencentCloud\Vod\V20180717\Models\CreateProcedureTemplateRequest;
use TencentCloud\Vod\V20180717\Models\DescribeProcedureTemplatesRequest;

class Vod
{
    protected $client;

    /**
     * 获取上传签名
     * @return string
     * @throws \Exception
     */
    public function getUploadSignature()
    {
        $config = $this->config();

        $currentTime = time();
        $data = [
            'secretId' => $config['secret_id'],
            'currentTimeStamp' => $currentTime,
            'expireTime' => $currentTime + 86400,
            'random' => random_int(0, 100000),
            'vodSubAppId' => config('tencent.vod.app_id'),
        ];
        $queryString = http_build_query($data);
        return base64_encode(hash_hmac('sha1', $queryString, $config['secret_key'], true) . $queryString);
    }

    /**
     * 删除视频文件
     * @param array $fileIds
     * @return void
     */
    public function deleteVideos(array $fileIds): void
    {
        foreach ($fileIds as $fileId) {
            $req = new DeleteMediaRequest();
            $req->setFileId($fileId);
            try {
                // 这里只管提交不关注是否成功处理
                $this->initClient()->DeleteMedia($req);
            } catch (\Exception $e) {
                Log::error(__METHOD__ . '|腾讯云视频删除', ['err' => $e->getMessage(), 'fileId' => $fileId]);
            }
        }
    }

    /**
     * 获取子应用列表
     * @return array
     */
    public function appList()
    {
        $req = new DescribeSubAppIdsRequest();
        $result = $this->initClient()->DescribeSubAppIds($req);
        $total = $result->getTotalCount();
        $data = $result->getSubAppIdInfoSet();
        return compact('data', 'total');
    }

    /**
     * 创建子应用
     * @param string $name
     * @param string $description
     * @return int
     */
    public function appStore(string $name, string $description = '')
    {
        $req = new CreateSubAppIdRequest();
        $req->setName($name);
        $req->setDescription($description);
        $result = $this->initClient()->CreateSubAppId($req);
        return $result->getSubAppId();
    }

    public function domainList(string $subAppId, int $limit = 20, int $offset = 0)
    {
        $req = new DescribeVodDomainsRequest();
        $req->setSubAppId((int)$subAppId);
        $req->setLimit($limit);
        $req->setOffset($offset);
        $result = $this->initClient()->DescribeVodDomains($req);
        $total = $result->getTotalCount();
        $data = $result->getDomainSet();
        return compact('total', 'data');
    }

    public function domainStore(string $subAppId, string $domain)
    {
        $req = new CreateVodDomainRequest();
        $req->setSubAppId((int)$subAppId);
        $req->setDomain($domain);
        $this->initClient()->CreateVodDomain($req);
    }

    /**
     * @param string $subAppId
     * @param string $domain
     * @param RefererAuthPolicy $refererAuthPolicy
     * @param UrlSignatureAuthPolicy $urlSignatureAuthPolicy
     * @return void
     */
    public function domainConfigPut(string $subAppId, string $domain, RefererAuthPolicy $refererAuthPolicy, UrlSignatureAuthPolicy $urlSignatureAuthPolicy)
    {
        $req = new ModifyVodDomainConfigRequest();
        $req->setDomain($domain);
        $req->setSubAppId((int)$subAppId);
        $req->setRefererAuthPolicy($refererAuthPolicy);
        $req->setUrlSignatureAuthPolicy($urlSignatureAuthPolicy);
        $this->initClient()->ModifyVodDomainConfig($req);
    }

    /**
     * @param string $subAppId
     * @return array
     */
    public function eventConfig(string $subAppId)
    {
        $req = new DescribeEventConfigRequest();
        $req->setSubAppId((int)$subAppId);
        $result = $this->initClient()->DescribeEventConfig($req);

        return [
            'mode' => $result->getMode(),
            'notification_url' => $result->getNotificationUrl(),
        ];
    }

    /**
     * @param string $subAppId
     * @param string $url
     * @return void
     */
    public function eventConfigPut(string $subAppId, string $url)
    {
        $req = new ModifyEventConfigRequest();
        $req->setSubAppId((int)$subAppId);
        $req->setMode('PUSH');
        $req->setNotificationUrl($url);
        $req->setDeleteMediaCompleteEventSwitch('ON');
        $req->setUploadMediaCompleteEventSwitch('ON');
        $this->initClient()->ModifyEventConfig($req);
    }

    /**
     * 获取任务流列表
     * @param string $subAppId
     * @return array
     */
    public function procedureTemplatesList(string $subAppId)
    {
        $req = new DescribeProcedureTemplatesRequest();
        $req->setSubAppId((int)$subAppId);
        $req->setLimit(100);
        $result = $this->initClient()->DescribeProcedureTemplates($req);
        $data = [];
        foreach ($result->getProcedureTemplateSet() as $tmpItem) {
            $data[] = [
                'name' => $tmpItem->Name,
            ];
        }
        return [
            'total' => $result->getTotalCount(),
            'data' => $data,
        ];
    }

    /**
     * 创建多清晰度的不加密的任务
     * @param string $subAppId
     * @return void
     */
    public function procedureTemplateStoreSimple(string $subAppId)
    {
        $mediaProcessTask = new MediaProcessTaskInput();

        $transcodeTaskInput720 = new TranscodeTaskInput();
        $transcodeTaskInput720->setDefinition(100230);//hls-720p

        $transcodeTaskInput1080 = new TranscodeTaskInput();
        $transcodeTaskInput1080->setDefinition(100240);//hls-1080p

        $mediaProcessTask->setTranscodeTaskSet([
            $transcodeTaskInput720,
            $transcodeTaskInput1080,
        ]);

        $req = new CreateProcedureTemplateRequest();
        $req->setSubAppId((int)$subAppId);
        $req->setName('MeEduSimple');
        $req->setComment('此任务由meedu程序自动创建-不加密');
        $req->setMediaProcessTask($mediaProcessTask);

        $this->initClient()->CreateProcedureTemplate($req);
    }

    protected function config(): array
    {
        /**
         * @var ConfigService $configService
         */
        $configService = app()->make(ConfigServiceInterface::class);
        return $configService->getTencentVodConfig();
    }

    protected function initClient()
    {
        if (!$this->client) {
            $config = $this->config();
            $credential = new \TencentCloud\Common\Credential($config['secret_id'], $config['secret_key']);
            $this->client = new \TencentCloud\Vod\V20180717\VodClient($credential, '');
        }
        return $this->client;
    }
}

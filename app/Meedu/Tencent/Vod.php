<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Tencent;

use Illuminate\Support\Str;
use App\Constant\TencentConstant;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceException;
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
use TencentCloud\Vod\V20180717\Models\DescribeMediaInfosRequest;
use TencentCloud\Vod\V20180717\Models\DescribeVodDomainsRequest;
use TencentCloud\Vod\V20180717\Models\DescribeEventConfigRequest;
use TencentCloud\Vod\V20180717\Models\ModifyVodDomainConfigRequest;
use TencentCloud\Vod\V20180717\Models\CreateProcedureTemplateRequest;
use TencentCloud\Vod\V20180717\Models\ProcessMediaByProcedureRequest;
use TencentCloud\Vod\V20180717\Models\DescribeProcedureTemplatesRequest;

class Vod
{
    protected $client;

    /**
     * @param $url
     * @param int $trySeconds
     * @return string
     * @throws ServiceException
     */
    public function url($url, int $trySeconds = 0): string
    {
        $config = $this->config();
        if (!$config['play_key']) {
            throw new ServiceException(__('腾讯云点播未配置：:msg', ['msg' => '播放域名的key未配置']));
        }

        $urlInfo = parse_url($url);//解析url
        $dir = pathinfo($urlInfo['path'], PATHINFO_DIRNAME) . '/';//解析url中的path
        $t = dechex(time() + 3600 * 3);//有效请3小时
        $ipLimit = 1;//限制IP访问数量
        $us = Str::random(6);//随机字符串[防止重入攻击]
        $sign = md5($config['play_key'] . $dir . $t . $trySeconds . $ipLimit . $us);

        return sprintf('%s?t=%s&exper=%d&rlimit=%d&us=%s&sign=%s', $url, $t, $trySeconds, $ipLimit, $us, $sign);
    }

    /**
     * @param string $fileId
     * @param int $trySeconds
     * @param string $mode
     * @return string
     * @throws ServiceException
     */
    public function getPlayerSign(string $fileId, int $trySeconds, string $mode): string
    {
        $config = $this->config();
        if (!$config['domain']) {
            throw new ServiceException(__('腾讯云点播未配置：:msg', ['msg' => '播放域名']));
        }
        if (!$config['play_key']) {
            throw new ServiceException(__('腾讯云点播未配置：:msg', ['msg' => '播放域名的key']));
        }

        $contentInfo = [
            'audioVideoType' => 'Original',
        ];
        if ($mode === TencentConstant::VOD_TRANSCODE_SIMPLE_TASK) {
            $contentInfo['audioVideoType'] = 'Transcode';
            $contentInfo['transcodeDefinition'] = 100230;
        } elseif ($mode === TencentConstant::VOD_TRANSCODE_ADAPTIVE) {
            $contentInfo['audioVideoType'] = 'ProtectedAdaptive';
            $contentInfo['drmAdaptiveInfo'] = [
                'privateEncryptionDefinition' => 12,
            ];
        }

        $data = [
            'appId' => (int)$config['app_id'],
            'fileId' => $fileId,
            'contentInfo' => $contentInfo,
            'currentTimeStamp' => time(),
            'expireTimeStamp' => time() + 3600 * 3, //3小时
            'urlAccessInfo' => [
                'rlimit' => 1,//仅允许1个ip访问
                'us' => Str::random(6),//随机字符串
                'exper' => $trySeconds, //试看秒数
                'domain' => $config['domain'],//播放域名
                'scheme' => 'HTTPS',//播放协议
            ],
        ];

        $headerEncode = $this->base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256'], JSON_UNESCAPED_SLASHES));
        $dataEncode = $this->base64UrlEncode(json_encode($data, JSON_UNESCAPED_SLASHES));
        $sign = $this->base64UrlEncode(hash_hmac('sha256', $headerEncode . '.' . $dataEncode, $config['play_key'], true));

        return $headerEncode . '.' . $dataEncode . '.' . $sign;
    }

    /**
     * @param string $input
     * @return string
     */
    private function base64UrlEncode(string $input): string
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

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
     * @param array $rules
     * @return void
     */
    public function deleteVideos(array $fileIds, array $rules = []): void
    {
        foreach ($fileIds as $fileId) {
            $req = new DeleteMediaRequest();
            $req->setFileId($fileId);
            if ($rules) {
                $parts = [];
                // OriginalFiles,TranscodeFiles,AdaptiveDynamicStreamingFiles,WechatPublishFiles
                foreach ($rules as $ruleItem) {
                    $parts[] = ['Type' => $ruleItem];
                }
                $req->setDeleteParts($parts);
            }
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
     * 获取默认任务流列表
     * @return array
     */
    public function defaultProcedureTemplatesList()
    {
        $config = $this->config();
        return $this->procedureTemplatesList($config['app_id']);
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

    public function transcodeSubmit(string $fileId, string $name)
    {
        $config = $this->config();
        $req = new ProcessMediaByProcedureRequest();
        $req->setSubAppId((int)$config['app_id']);
        $req->setFileId($fileId);
        $req->setProcedureName($name);
        $this->initClient()->ProcessMediaByProcedure($req);
    }

    /**
     * @param string $fileId
     * @param int $trySeconds
     * @param string $mode
     * @return array
     * @throws ServiceException
     */
    public function getPlayUrls(string $fileId, int $trySeconds, string $mode)
    {
        $config = $this->config();
        $req = new DescribeMediaInfosRequest();
        $req->setSubAppId((int)$config['app_id']);
        $req->setFileIds([$fileId]);
        $req->setFilters(['basicInfo', 'metaData', 'transcodeInfo', 'adaptiveDynamicStreamingInfo']);
        $result = $this->initClient()->DescribeMediaInfos($req);
        $mediaInfoSet = $result->getMediaInfoSet();
        if (!$mediaInfoSet) {
            throw new ServiceException(__('无播放地址'));
        }

        $mediaInfo = $mediaInfoSet[0];//因为上面FileIds[]仅传递了一个fileId,因此index=0的就是查询的视频
        $basicInfo = $mediaInfo->BasicInfo;//视频基础信息
        $metaData = $mediaInfo->MetaData;//视频元数据
        $duration = (int)$metaData->VideoDuration;//视频时长

        $data = [];
        if (TencentConstant::VOD_TRANSCODE_SIMPLE_TASK === $mode) {
            if (!property_exists($mediaInfo, 'TranscodeInfo') || !$mediaInfo->TranscodeInfo->TranscodeSet) {
                throw new ServiceException(__('无播放地址'));
            }
            foreach ($mediaInfo->TranscodeInfo->TranscodeSet as $setItem) {
                $data[] = [
                    'url' => $this->url($setItem->Url, $trySeconds),
                    'format' => pathinfo($setItem->Url, PATHINFO_EXTENSION),
                    'name' => $setItem->Width,
                    'duration' => $duration,
                ];
            }
        } elseif (TencentConstant::VOD_TRANSCODE_ADAPTIVE === $mode) {
            if (
                !property_exists($mediaInfo, 'AdaptiveDynamicStreamingInfo')
                || !$mediaInfo->AdaptiveDynamicStreamingInfo->AdaptiveDynamicStreamingSet
            ) {
                throw new ServiceException(__('无播放地址'));
            }
            foreach ($mediaInfo->AdaptiveDynamicStreamingInfo->AdaptiveDynamicStreamingSet as $setItem) {
                $data[] = [
                    'url' => $this->url($setItem->Url, $trySeconds),
                    'format' => pathinfo($setItem->Url, PATHINFO_EXTENSION),
                    'name' => '自适应',
                    'duration' => $duration,
                ];
            }
        } else {
            $data[] = [
                'url' => $this->url($basicInfo->MediaUrl, $trySeconds),
                'format' => pathinfo($basicInfo->MediaUrl, PATHINFO_EXTENSION),
                'name' => $metaData->Width,
                'duration' => $duration,
            ];
        }

        return $data;
    }

    protected function config(): array
    {
        /**
         * @var ConfigService $configService
         */
        $configService = app()->make(ConfigServiceInterface::class);
        $config = $configService->getTencentVodConfig();
        $config['play_key'] = $configService->getTencentVodPlayKey();
        return $config;
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

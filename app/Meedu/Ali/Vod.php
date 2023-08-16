<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Ali;

use Illuminate\Support\Str;
use App\Constant\AliConstant;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceException;
use AlibabaCloud\Client\AlibabaCloud;
use App\Services\Base\Services\ConfigService;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class Vod
{
    public const API_VERSION = '2017-03-21';

    /**
     * @var ConfigService
     */
    protected $configService;

    private $errMsg;

    /**
     * @param string $msg
     * @return void
     */
    public function setErrMsg(string $msg): void
    {
        $this->errMsg = $msg;
    }

    /**
     * @return mixed
     */
    public function getErrMsg()
    {
        return $this->errMsg;
    }

    public function __construct(ConfigServiceInterface $configService)
    {
        $this->configService = $configService;
    }

    /**
     * 删除视频
     * @param array $fileIds
     * @return bool
     */
    public function deleteVideos(array $fileIds): bool
    {
        // 批量删除这里不做强制的结果绑定
        // 也就是我提交了删除的行为，具体能不能删掉不关注
        // 这里仅记录操作的结果用作debug
        try {
            $this->client()
                ->action('DeleteVideo')
                ->options(['query' => ['VideoIds' => implode(',', $fileIds)]])
                ->request();
            return true;
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            Log::error(__METHOD__ . '|阿里云批量删除视频-失败', ['err' => $e->getMessage(), 'fileIds' => $fileIds]);
            return false;
        }
    }

    /**
     * 子应用列表
     * @param int $page
     * @param int $size
     * @return \AlibabaCloud\Client\Result\Result|false
     */
    public function apps(int $page = 1, int $size = 100)
    {
        try {
            $result = $this->client()
                ->action('ListAppInfo')
                ->options(['PageNo' => $page, 'PageSize' => $size])
                ->request();

            return $result;
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * 创建子应用
     * @param string $name
     * @param string $description
     * @return \AlibabaCloud\Client\Result\Result|false
     */
    public function appStore(string $name, string $description = '')
    {
        try {
            $result = $this->client()
                ->action('CreateAppInfo')
                ->options(['AppName' => $name, 'Description' => $description])
                ->request();

            return $result;
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * 加速域名列表
     * @param int $page
     * @param int $size
     * @return false|mixed
     */
    public function domains(int $page = 1, int $size = 50)
    {
        try {
            $result = $this->client()
                ->action('DescribeVodUserDomains')
                ->options(['PageNumber' => $page, 'PageSize' => $size])
                ->request();

            return [
                'total' => $result['TotalCount'],
                'data' => $result['Domains'],
            ];
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * 域名配置
     * @param string $domain
     * @return bool
     */
    public function domainDefaultConfig(string $domain): bool
    {
        try {
            $this->client()
                ->action('BatchSetVodDomainConfigs')
                ->options([
                    'DomainNames' => $domain,
                    'Functions' => [
                        // cors
                        [
                            'functionArgs' => [
                                [
                                    'argName' => 'Access-Control-Allow-Origin',
                                    'argValue' => '*',
                                ],
                            ],
                            'functionName' => 'set_resp_header',
                        ],
                        // key
                        [
                            'functionArgs' => [
                                [
                                    'argName' => 'auth_key1',
                                    'argValue' => Str::random(32),
                                ],
                                [
                                    'argName' => 'auth_key2',
                                    'argValue' => Str::random(32),
                                ],
                            ],
                            'functionName' => 'aliauth',
                        ],
                        // video_seek
                        [
                            'functionArgs' => [
                                [
                                    'argName' => 'enable',
                                    'argValue' => 'on',
                                ],
                            ],
                            'functionName' => 'video_seek',
                        ],
                    ],
                ])
                ->request();

            return true;
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * 获取event
     * @param string $appId
     * @return \AlibabaCloud\Client\Result\Result|false
     */
    public function eventQuery(string $appId = '')
    {
        try {
            $result = $this->client()
                ->action('GetMessageCallback')
                ->options(['AppId' => $appId])
                ->request();

            return $result['MessageCallback'];
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * event设置
     * @param string $appId
     * @param string $url
     * @param string $key
     * @return bool
     */
    public function eventStore(string $appId, string $url, string $key): bool
    {
        try {
            $this->client()
                ->action('SetMessageCallback')
                ->method('POST')
                ->debug(true)
                ->options([
                    'form_params' => [
                        'AppId' => $appId,
                        'CallbackURL' => $url,
                        'CallbackType' => 'HTTP',
                        'EventTypeList' => 'ALL',//监听全部事件
                        'AuthSwitch' => 'on',//开启鉴权
                        'AuthKey' => $key,
                    ],
                ])
                ->request();
            return true;
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * event删除
     * @param string $appId
     * @return \AlibabaCloud\Client\Result\Result|false
     */
    public function eventDestroy(string $appId): bool
    {
        try {
            $this->client()
                ->action('DeleteMessageCallback')
                ->options(['AppId' => $appId])
                ->request();
            return true;
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * 转码模板列表
     * @param string $appId
     * @return false|mixed
     */
    public function templates(string $appId)
    {
        try {
            $data = [];
            $appId && $data['AppId'] = $appId;
            $result = $this->client()
                ->action('ListTranscodeTemplateGroup')
                ->options($data)
                ->request();
            return $result['TranscodeTemplateGroupList'];
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * 创建转码模板
     * @param string $appId
     * @param bool $isEncrypt
     * @return false|mixed
     */
    public function templateStore(string $appId, bool $isEncrypt = false)
    {
        $hdConfig = [
            'Type' => 'Normal',
            'Definition' => 'HD',
            'TemplateName' => '高清',
            'Video' => [
                'Codec' => 'H.264',
                'Bitrate' => '1500',
                'Width' => '1280',
                'Height' => '720',
                'Remove' => 'false',
                'Fps' => '25',
            ],
            'Audio' => [
                'Codec' => 'AAC',
                'Bitrate' => '128',
                'Samplerate' => '44100',
            ],
            //封装音视频码流的容器格式
            'Container' => [
                'Format' => 'm3u8',
            ],
            //转码的分片设置参数HLS必传
            'MuxConfig' => [
                'Segment' => [
                    'Duration' => 10,
                ],
            ],
        ];

        $fhdConfig = [
            'Type' => 'Normal',
            'Definition' => 'FHD',
            'TemplateName' => '超清',
            'Video' => [
                'Codec' => 'H.264',
                'Bitrate' => '3000',
                'Width' => '1920',
                'Height' => '1080',
                'Remove' => 'false',
                'Fps' => '25',
            ],
            'Audio' => [
                'Codec' => 'AAC',
                'Bitrate' => '160',
                'Samplerate' => '44100',
            ],
            //封装音视频码流的容器格式
            'Container' => [
                'Format' => 'm3u8',
            ],
            //转码的分片设置参数HLS必传
            'MuxConfig' => [
                'Segment' => [
                    'Duration' => 10,
                ],
            ],
        ];

        if ($isEncrypt) {
            $hdConfig['EncryptSetting'] = [
                'EncryptType' => 'AliyunVoDEncryption',
            ];
            $fhdConfig['EncryptSetting'] = [
                'EncryptType' => 'AliyunVoDEncryption',
            ];
        }

        $name = AliConstant::VOD_TRANSCODE_SIMPLE;
        if ($isEncrypt) {
            $name = AliConstant::VOD_TRANSCODE_HLS_SIMPLE;
        }

        try {
            $result = $this->client()
                ->action('AddTranscodeTemplateGroup')
                ->method('POST')
                ->options([
                    'form_params' => [
                        'AppId' => $appId,
                        'Name' => $name,
                        'TranscodeTemplateList' => json_encode([$hdConfig, $fhdConfig]),
                    ],
                ])
                ->request();

            return $result['TranscodeTemplateGroupId'];
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * 获取视频的播放地址
     * @param string $videoId
     * @param array $params
     * @return false|mixed
     */
    public function playInfo(string $videoId, array $params = [])
    {
        try {
            $data = ['VideoId' => $videoId];
            $params && $data = array_merge($data, $params);
            $result = $this->client()
                ->action('GetPlayInfo')
                ->method('POST')
                ->options(['form_params' => $data])
                ->request();
            return $result['PlayInfoList'];
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * @param string $videoId
     * @return bool
     * @throws ServiceException
     */
    public function deleteStream(string $videoId): bool
    {
        $playList = $this->playInfo($videoId, ['ResultType' => 'Multiple']);
        if ($playList === false) {
            if (Str::startsWith($this->getErrMsg(), 'InvalidVideo.NoneStream')) {//该视频只有源文件
                return true;
            }
            throw new ServiceException($this->getErrMsg());
        }

        $jobIds = array_column($playList['PlayInfo'], 'JobId');
        if (!$jobIds) {
            return true;
        }

        try {
            $this->client()
                ->action('DeleteStream')
                ->method('POST')
                ->options([
                    'form_params' => [
                        'VideoId' => $videoId,
                        'JobIds' => implode(',', $jobIds),
                    ],
                ])
                ->request();
            return true;
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * @param string $videoId
     * @param string $templateId
     * @param array $extra
     * @return bool
     */
    public function transcodeSubmit(string $videoId, string $templateId, array $extra = []): bool
    {
        try {
            $data = [
                'VideoId' => $videoId,
                'TemplateGroupId' => $templateId,
            ];
            $extra && $data = array_merge($data, $extra);
            $this->client()
                ->action('SubmitTranscodeJobs')
                ->method('POST')
                ->options(['form_params' => $data])
                ->request();
            return true;
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    public function generateKMSDataKey()
    {
        try {
            return $this->client()
                ->action('GenerateKMSDataKey')
                ->request();
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * @param string $cipherText
     * @return false|mixed
     */
    public function decryptKMSDataKey(string $cipherText)
    {
        try {
            $result = $this->client()
                ->action('DecryptKMSDataKey')
                ->method('POST')
                ->options([
                    'form_params' => [
                        'CipherText' => $cipherText,
                    ],
                ])
                ->request();
            return $result['Plaintext'];
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * 创建视频上传token
     * @param string $fileName
     * @param string $title
     * @return array|false
     */
    public function createUploadVideo(string $fileName, string $title)
    {
        $config = $this->config();
        if (!$config['access_key_id'] || !$config['access_key_secret']) {
            throw new ServiceException(__('阿里云点播未配置：:msg', ['msg' => 'AccessKeyId,AccessKeySecret']));
        }
        if (!$config['callback_key']) {
            throw new ServiceException(__('阿里云点播未配置：:msg', ['msg' => '事件回调']));
        }

        try {
            $result = $this->client()
                ->action('CreateUploadVideo')
                ->options([
                    'query' => [
                        'Title' => $title,
                        'FileName' => $fileName,
                    ]
                ])
                ->request();

            return [
                'upload_auth' => $result['UploadAuth'],
                'upload_address' => $result['UploadAddress'],
                'video_id' => $result['VideoId'],
                'request_id' => $result['RequestId'],
            ];
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * @param string $fileId
     * @return array|false
     */
    public function refreshUploadVideo(string $fileId)
    {
        try {
            $result = $this->client()
                ->action('RefreshUploadVideo')
                ->options([
                    'query' => [
                        'VideoId' => $fileId,
                    ]
                ])
                ->request();

            return [
                'upload_auth' => $result['UploadAuth'],
                'upload_address' => $result['UploadAddress'],
                'video_id' => $result['VideoId'],
                'request_id' => $result['RequestId'],
            ];
        } catch (\Exception $e) {
            $this->setErrMsg($e->getMessage());
            exception_record($e);
            return false;
        }
    }

    /**
     * 阿里云请求client
     * @return \AlibabaCloud\Client\Request\RpcRequest
     * @throws \AlibabaCloud\Client\Exception\ClientException
     */
    protected function client()
    {
        $config = $this->config();

        AlibabaCloud::accessKeyClient($config['access_key_id'], $config['access_key_secret'])
            ->regionId($config['region'])
            ->connectTimeout(3)
            ->timeout(30)
            ->asDefaultClient();

        return AlibabaCloud::rpc()->product('vod')->host($config['host'])->version(self::API_VERSION);
    }

    /**
     * 获取阿里云配置
     * @return array
     */
    protected function config(): array
    {
        return $this->configService->getAliVodConfig();
    }
}

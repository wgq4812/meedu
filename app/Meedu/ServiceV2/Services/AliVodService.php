<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use Carbon\Carbon;
use App\Meedu\Ali\Vod;
use App\Constant\AliConstant;
use Illuminate\Support\Facades\Log;
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

    public function saveEventConfig(string $callbackKey, string $callbackUrl, string $appId)
    {
        $result = $this->vod->eventStore($appId, $callbackUrl, $callbackKey);
        if (!$result) {
            throw new ServiceException($this->vod->getErrMsg());
        }
    }

    public function transcodeTemplateStore(string $appId, bool $isEncrypt): string
    {
        $result = $this->vod->templateStore($appId, $isEncrypt);
        if (!$result) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        return $result;
    }

    public function domains(int $page = 1, int $size = 50): array
    {
        $data = $this->vod->domains($page, $size);
        if ($data === false) {
            throw new ServiceException($this->vod->getErrMsg());
        }
    }

    public function transcodeSubmit(string $appId, string $fileId, string $tempName, string $tempId): void
    {
        // 重复提交判断
        $record = $this->vodDao->findAliTranscodeRecord($fileId, $tempName);
        if ($record) {
            throw new ServiceException(__(
                '请勿重复提交转码。最近提交时间：:date',
                [
                    'date' => Carbon::parse($record['created_at'])->format('Y-m-d H:i:s'),
                ]
            ));
        }

        // 提交转码
        $extra = [];
        if ($tempName === AliConstant::VOD_TRANSCODE_HLS_SIMPLE) {//HLS标准加密
            $key = $this->vod->generateKMSDataKey();
            if ($key === false) {
                throw new ServiceException($this->vod->getErrMsg());
            }
            $extra = [
                'EncryptConfig' => json_encode([
                    'CipherText' => $key['CiphertextBlob'],
                    'DecryptKeyUri' => route('ali.vod.play.hls', ['Ciphertext' => $key['CiphertextBlob']]),
                    'KeyServiceType' => 'KMS',
                ]),
            ];
        }
        $result = $this->vod->transcodeSubmit($fileId, $tempId, $extra);
        if ($result === false) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        $this->vodDao->storeAliTranscodeRecord($fileId, $tempName, $tempId);
    }

    public function transcodeDestroy(string $fileId): void
    {
        $result = $this->vod->deleteStream($fileId);
        if (!$result) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        $this->vodDao->cleanAliTranscodeRecordsMulti([$fileId]);
    }

    public function transcodeTemplates(string $appId): array
    {
        $result = $this->vod->templates($appId);
        if ($result === false) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        return $result;
    }

    public function getTranscodeRecords(string $fileId): array
    {
        return $this->vodDao->getAliTranscodeRecords([$fileId], '');
    }

    public function chunks(array $fileIds): array
    {
        return $this->vodDao->getAliTranscodeRecords($fileIds, '');
    }

    public function destroyMulti(array $fileIds): void
    {
        $this->vod->deleteVideos($fileIds);
        $this->vodDao->cleanAliTranscodeRecordsMulti($fileIds);
    }

    public function createUploadToken(string $fileName, string $title): array
    {
        $result = $this->vod->createUploadVideo($fileName, $title);
        if ($result === false) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        return $result;
    }

    public function createUploadRefreshToken(string $fileId): array
    {
        $result = $this->vod->refreshUploadVideo($fileId);
        if ($result === false) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        return $result;
    }

    public function decryptKMSDataKey(string $key): string
    {
        $result = $this->vod->decryptKMSDataKey($key);
        if ($result === false) {
            throw new ServiceException($this->vod->getErrMsg());
        }
        return $result;
    }

    public function playInfo(string $fileId, array $extra): array
    {
        $result = $this->vod->playInfo($fileId, $extra);
        if ($result === false) {
            Log::error(__METHOD__ . '|无法获取视频播放地址', ['err' => $this->vod->getErrMsg(), 'file_id' => $fileId]);
            throw new ServiceException(__('无法获取播放地址'));
        }
        return $result;
    }
}

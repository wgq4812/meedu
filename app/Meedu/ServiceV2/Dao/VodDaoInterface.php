<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

interface VodDaoInterface
{
    public function getTencentTranscodeRecords(array $fileIds, string $templateName): array;

    public function storeTencentTranscodeRecord(string $fileId, string $templateName): void;

    public function clearTencentTranscodeRecords(array $fileIds): void;

    public function findTencentTranscodeRecord(string $fileId, string $tempName): array;

    // ------- 友情分割线 -------

    public function findAliTranscodeRecord(string $fileId, string $tempName): array;

    public function getAliTranscodeRecords(array $fileIds, string $templateName): array;

    public function storeAliTranscodeRecord(string $fileId, string $templateName, string $templateId): void;

    public function cleanAliTranscodeRecordsMulti(array $fileIds): void;
}

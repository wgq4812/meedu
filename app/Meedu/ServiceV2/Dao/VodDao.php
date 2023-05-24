<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

use App\Meedu\ServiceV2\Models\AliVideoTranscode;
use App\Meedu\ServiceV2\Models\TencentVideoTranscode;

class VodDao implements VodDaoInterface
{
    public function getTencentTranscodeRecords(array $fileIds, string $templateName): array
    {
        return TencentVideoTranscode::query()
            ->whereIn('file_id', $fileIds)
            ->where('template_name', $templateName)
            ->get()
            ->toArray();
    }

    public function storeTencentTranscodeRecord(string $fileId, string $templateName): void
    {
        TencentVideoTranscode::create([
            'file_id' => $fileId,
            'template_name' => $templateName,
        ]);
    }

    public function clearTencentTranscodeRecords(array $fileIds): void
    {
        TencentVideoTranscode::query()->whereIn('file_id', $fileIds)->delete();
    }

    public function getAliTranscodeRecords(array $fileIds, string $templateName): array
    {
        return AliVideoTranscode::query()
            ->whereIn('file_id', $fileIds)
            ->where('template_name', $templateName)
            ->get()
            ->toArray();
    }

    public function storeAliTranscodeRecord(string $fileId, string $templateName, string $templateId): void
    {
        AliVideoTranscode::create([
            'file_id' => $fileId,
            'template_name' => $templateName,
            'template_id' => $templateId,
        ]);
    }

    public function cleanAliTranscodeRecords(string $fileId): void
    {
        AliVideoTranscode::query()->where('file_id', $fileId)->delete();
    }

    public function findAliTranscodeRecord(string $fileId, string $templateId): array
    {
        $data = AliVideoTranscode::query()->where('file_id', $fileId)->first();
        return $data ? $data->toArray() : [];
    }
}

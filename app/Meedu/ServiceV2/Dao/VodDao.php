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
            ->when($templateName, function ($query) use ($templateName) {
                $query->where('template_name', $templateName);
            })
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

    public function findTencentTranscodeRecord(string $fileId, string $tempName): array
    {
        $data = TencentVideoTranscode::query()->where('file_id', $fileId)->where('template_name', $tempName)->first();
        return $data ? $data->toArray() : [];
    }

    // ------- 友情分割线 -------

    public function getAliTranscodeRecords(array $fileIds, string $templateName): array
    {
        return AliVideoTranscode::query()
            ->whereIn('file_id', $fileIds)
            ->when($templateName, function ($query) use ($templateName) {
                $query->where('template_name', $templateName);
            })
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

    public function cleanAliTranscodeRecordsMulti(array $fileIds): void
    {
        AliVideoTranscode::query()->whereIn('file_id', $fileIds)->delete();
    }

    public function findAliTranscodeRecord(string $fileId, string $tempName): array
    {
        $data = AliVideoTranscode::query()->where('file_id', $fileId)->where('template_name', $tempName)->first();
        return $data ? $data->toArray() : [];
    }
}

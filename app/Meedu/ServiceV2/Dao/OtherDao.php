<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

use Carbon\Carbon;
use App\Meedu\ServiceV2\Models\Nav;
use App\Meedu\ServiceV2\Models\Link;
use App\Meedu\ServiceV2\Models\SmsRecord;
use App\Meedu\ServiceV2\Models\ViewBlock;
use App\Meedu\ServiceV2\Models\Announcement;
use App\Meedu\ServiceV2\Models\UserUploadImage;

class OtherDao implements OtherDaoInterface
{
    public function storeUserUploadImage(
        int    $userId,
        string $group,
        string $disk,
        string $path,
        string $name,
        string $visitUrl,
        string $logApi,
        string $logIp,
        string $logUA
    ): void {
        UserUploadImage::create([
            'user_id' => $userId,
            'group' => $group,
            'disk' => $disk,
            'path' => $path,
            'name' => $name,
            'visit_url' => $visitUrl,
            'log_api' => $logApi,
            'log_ip' => $logIp,
            'log_ua' => $logUA,
            'created_at' => Carbon::now()->toDateTimeLocalString(),
        ]);
    }

    public function navs(): array
    {
        return Nav::query()
            ->select(['id', 'sort', 'name', 'url', 'active_routes', 'platform', 'parent_id', 'blank'])
            ->orderBy('sort')
            ->get()
            ->toArray();
    }

    public function links(): array
    {
        return Link::query()
            ->select(['id', 'sort', 'name', 'url'])
            ->orderBy('sort')
            ->get()
            ->toArray();
    }

    public function viewBlocks(string $page, string $platform): array
    {
        return ViewBlock::query()
            ->select(['id', 'platform', 'page', 'sign', 'sort', 'config'])
            ->where('page', $page)
            ->where('platform', $platform)
            ->orderBy('sort')
            ->get()
            ->toArray();
    }

    public function latestAnnouncement(): array
    {
        $a = Announcement::query()
            ->select(['id', 'admin_id', 'announcement', 'created_at', 'view_times', 'title'])
            ->orderByDesc('id')
            ->first();
        return $a ? $a->toArray() : [];
    }

    public function findAnnouncement(int $id): array
    {
        $data = Announcement::query()->where('id', $id)->first();
        return $data ? $data->toArray() : [];
    }

    public function announcementPaginate(int $page, int $size): array
    {
        $data = Announcement::query()->orderByDesc('id')->forPage($page, $size)->get()->toArray();
        $total = Announcement::query()->orderByDesc('id')->count();
        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    public function storeSmsRecord(array $data): void
    {
        SmsRecord::create($data);
    }


}

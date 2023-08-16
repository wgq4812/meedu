<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

interface OtherDaoInterface
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
    ): void;

    public function navs(): array;

    public function links(): array;

    public function viewBlocks(string $page, string $platform): array;

    public function latestAnnouncement(): array;

    public function findAnnouncement(int $id): array;

    public function announcementPaginate(int $page, int $size): array;
}

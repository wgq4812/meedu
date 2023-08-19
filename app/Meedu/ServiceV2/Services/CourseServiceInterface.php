<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface CourseServiceInterface
{
    public function findOrFail(int $id): array;

    public function videoFindOrFail(int $videoId, int $courseId): array;

    public function chunk(array $ids, array $fields, array $params, array $with, array $withCount): array;

    public function videoChunk(array $ids, array $fields, array $params, array $with, array $withCount): array;

    public function getCoursePublishedVideos(int $courseId, array $fields): array;

    public function categories(): array;

    public function categoriesWithChildren(): array;

    public function coursePaginate(int $page, int $size, array $params, array $with, array $withCount): array;

    public function findAttachment(int $id, int $courseId): array;

    public function attachmentDownloadTimesInc(int $id): void;
}

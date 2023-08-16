<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

interface CourseDaoInterface
{
    public function chunk(array $ids, array $fields, array $params, array $with, array $withCount): array;

    public function videoChunk(array $ids, array $fields, array $params, array $with, array $withCount): array;

    public function getCoursePublishedVideoIds(int $courseId): array;

    public function findOrFail(int $id): array;

    public function videoFindOrFail(int $videoId, int $courseId): array;

    public function categories(): array;

    public function coursePaginate(int $page, int $size, array $params, array $with, array $withCount): array;

    public function findCourseAttach(int $id, int $courseId): array;

    public function attachDownloadTimesInc(int $id, int $count): void;
}

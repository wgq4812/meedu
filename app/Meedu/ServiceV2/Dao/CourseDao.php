<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Dao;

use Carbon\Carbon;
use App\Meedu\ServiceV2\Models\Course;
use App\Meedu\ServiceV2\Models\CourseVideo;
use App\Meedu\ServiceV2\Models\CourseAttach;
use App\Meedu\ServiceV2\Models\CourseCategory;

class CourseDao implements CourseDaoInterface
{
    public function chunk(array $ids, array $fields, array $params, array $with, array $withCount): array
    {
        return Course::query()
            ->select($fields)
            ->with($with)
            ->withCount($withCount)
            ->whereIn('id', $ids)
            ->when($params, function ($query) use ($params) {
                if (isset($params['category_id'])) {
                    $ids = [$params['category_id']];
                    $childrenIds = CourseCategory::query()
                        ->select(['id'])
                        ->where('parent_id', $params['category_id'])
                        ->get()
                        ->pluck('id')
                        ->toArray();
                    $childrenIds && $ids = array_merge($ids, $childrenIds);
                    $query->whereIn('category_id', $ids);
                }
                if (isset($params['lte_published_at'])) {
                    $query->where('published_at', '<=', $params['lte_published_at']);
                }
                if (isset($params['is_show'])) {
                    $query->where('is_show', $params['is_show']);
                }
                if (isset($params['charge'])) {
                    $query->where('charge', $params['charge']);
                }
                if (isset($params['is_free'])) {
                    $query->where('is_free', $params['is_free']);
                }
            })
            ->get()
            ->toArray();
    }

    public function videoChunk(array $ids, array $fields, array $params, array $with, array $withCount): array
    {
        return CourseVideo::query()
            ->select($fields)
            ->with($with)
            ->withCount($withCount)
            ->whereIn('id', $ids)
            ->when($params, function ($query) use ($params) {
                if (isset($params['lte_published_at'])) {
                    $query->where('published_at', '<=', $params['lte_published_at']);
                }
                if (isset($params['is_show'])) {
                    $query->where('is_show', $params['is_show']);
                }
                if (isset($params['charge'])) {
                    $query->where('charge', $params['charge']);
                }
            })
            ->get()
            ->toArray();
    }

    public function getCoursePublishedVideoIds(int $courseId): array
    {
        return CourseVideo::query()
            ->where('course_id', $courseId)
            ->select(['id'])
            ->where('published_at', '<=', Carbon::now())
            ->where('is_show', 1)
            ->get()
            ->pluck('id')
            ->toArray();
    }

    public function findOrFail(int $id): array
    {
        return Course::query()->where('id', $id)->firstOrFail()->toArray();
    }

    public function videoFindOrFail(int $videoId, int $courseId): array
    {
        return CourseVideo::query()->where('id', $videoId)->where('course_id', $courseId)->firstOrFail()->toArray();
    }

    public function categories(): array
    {
        return CourseCategory::query()
            ->select(['id', 'sort', 'name', 'parent_id', 'parent_chain'])
            ->orderBy('sort')
            ->get()
            ->toArray();
    }

    public function coursePaginate(int $page, int $size, array $params, array $with, array $withCount): array
    {
        $data = Course::query()
            ->select([
                'id', 'title', 'slug', 'thumb', 'charge',
                'short_description', 'published_at', 'is_show', 'category_id',
                'is_rec', 'user_count', 'is_free',
            ])
            ->with($with)
            ->withCount($withCount)
            ->where('is_show', 1)
            ->when($params, function ($query) use ($params) {
                if (isset($params['category_id'])) {
                    $ids = [$params['category_id']];
                    $childrenIds = CourseCategory::query()
                        ->select(['id'])
                        ->where('parent_id', $params['category_id'])
                        ->get()
                        ->pluck('id')
                        ->toArray();
                    $childrenIds && $ids = array_merge($ids, $childrenIds);
                    $query->whereIn('category_id', $ids);
                }
                if (isset($params['lte_published_at'])) {
                    $query->where('published_at', '<=', $params['lte_published_at']);
                }
                if (isset($params['is_show'])) {
                    $query->where('is_show', $params['is_show']);
                }
                if (isset($params['charge'])) {
                    $query->where('charge', $params['charge']);
                }
                if (isset($params['is_free'])) {
                    $query->where('is_free', $params['is_free']);
                }
            })
            ->orderByDesc('published_at')
            ->paginate(
                $size,
                ['*'],
                'page',
                $page
            );

        return [
            'data' => paginate_items_2array($data->items()),
            'total' => $data->total(),
        ];
    }

    public function findCourseAttach(int $id, int $courseId): array
    {
        $attach = CourseAttach::query()->where('id', $id)->where('course_id', $courseId)->first();
        return $attach ? $attach->toArray() : [];
    }

    public function attachDownloadTimesInc(int $id, int $count): void
    {
        CourseAttach::query()->where('id', $id)->increment('download_times', $count);
    }

}

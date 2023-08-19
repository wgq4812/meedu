<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Meedu\ServiceV2\Dao\CourseDaoInterface;
use App\Meedu\ServiceV2\Services\Traits\HashIdTrait;

class CourseService implements CourseServiceInterface
{
    use HashIdTrait;

    protected $courseDao;

    public function __construct(CourseDaoInterface $courseDao)
    {
        $this->courseDao = $courseDao;
    }

    public function chunk(array $ids, array $fields, array $params, array $with, array $withCount): array
    {
        return $this->courseDao->chunk($ids, $fields, $params, $with, $withCount);
    }

    public function videoChunk(array $ids, array $fields, array $params, array $with, array $withCount): array
    {
        return $this->courseDao->videoChunk($ids, $fields, $params, $with, $withCount);
    }

    public function getCoursePublishedVideos(int $courseId, array $fields): array
    {
        $videoIds = $this->courseDao->getCoursePublishedVideoIds($courseId);
        return $this->courseDao->videoChunk($videoIds, $fields, [], [], []);
    }

    public function findOrFail(int $id): array
    {
        return $this->courseDao->findOrFail($id);
    }

    public function videoFindOrFail(int $videoId, int $courseId): array
    {
        return $this->courseDao->videoFindOrFail($videoId, $courseId);
    }

    public function categories(): array
    {
        return $this->courseDao->categories();
    }

    public function categoriesWithChildren(): array
    {
        $categories = $this->courseDao->categories();
        $data = [];
        if ($categories) {
            foreach ($categories as $key => $item) {
                $categories[$key]['id'] = $this->idEncode($item['id'], 6);
            }
            $categories = collect($categories)->groupBy('parent_id');
            foreach ($categories[0] ?? [] as $item) {
                $tmp = $item;
                $tmp['children'] = $categories[$item['id']] ?? [];
                $data[] = $tmp;
            }
        }
        return $data;
    }


    public function coursePaginate(int $page, int $size, array $params, array $with, array $withCount): array
    {
        if (isset($params['category_id'])) {
            $params['category_id'] = $this->idDecode($params['category_id'], 6);
        }

        $data = $this->courseDao->coursePaginate($page, $size, $params, $with, $withCount);
        $categories = array_column($this->courseDao->categories(), null, 'id');

        if ($data['data']) {
            $tmpItems = $data['data'];
            foreach ($tmpItems as $key => $item) {
                $tmpItems[$key]['id'] = $this->idEncode($item['id'], 12);

                unset($tmpItems[$key]['category_id']);
                $tmpItems[$key]['category'] = null;
                if ($tmpCategoryItem = $categories[$item['category_id']]) {
                    $tmpItems[$key]['category'] = [
                        'id' => $this->idEncode($tmpCategoryItem['id'], 6),
                        'name' => $tmpCategoryItem['name'],
                    ];
                }
            }
            $data['data'] = $tmpItems;
        }

        return $data;
    }

    public function findAttachment(int $id, int $courseId): array
    {
        return $this->courseDao->findCourseAttach($id, $courseId);
    }

    public function attachmentDownloadTimesInc(int $id): void
    {
        $this->courseDao->attachDownloadTimesInc($id, 1);
    }

}

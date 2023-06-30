<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Bus\Order\Core;

use App\Constant\FrontendConstant;
use App\Exceptions\ServiceException;
use App\Meedu\ServiceV2\Services\UserServiceInterface;
use App\Meedu\ServiceV2\Services\CourseServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderCourse implements OrderInterface
{
    private $courseService;
    private $userService;

    public function __construct(CourseServiceInterface $courseService, UserServiceInterface $userService)
    {
        $this->courseService = $courseService;
        $this->userService = $userService;
    }

    public function check(int $userId, array $ids): void
    {
        $records = $this->userService->userCourseChunks($userId, $ids);
        if ($records) {
            throw new ServiceException(__('请勿重复购买'));
        }
    }

    public function goodsList(array $ids): array
    {
        $courses = $this->courseService->chunk($ids, ['id', 'title', 'thumb', 'charge'], [], [], []);
        if (!$courses) {
            throw new ModelNotFoundException();
        }
        $data = [];
        foreach ($courses as $courseItem) {
            $data[] = [
                'goods_id' => $courseItem['id'],
                'goods_type' => FrontendConstant::ORDER_TYPE_COURSE,
                'goods_name' => $courseItem['title'],
                'goods_thumb' => $courseItem['thumb'],
                'goods_charge' => $courseItem['charge'],
                'goods_ori_charge' => $courseItem['charge'],
                'num' => 1,
                'charge' => $courseItem['charge'],
            ];
        }
        return $data;
    }

    public function cancel(array $orderGoods): void
    {
    }

    public function refundConfirm(array $orderGoods): void
    {
    }
}

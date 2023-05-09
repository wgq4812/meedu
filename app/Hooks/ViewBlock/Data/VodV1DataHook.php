<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Hooks\ViewBlock\Data;

use App\Meedu\Hooks\HookParams;
use App\Meedu\ViewBlock\Constant;
use App\Meedu\Hooks\HookRuntimeInterface;
use App\Services\Course\Services\CourseService;
use App\Services\Course\Interfaces\CourseServiceInterface;

class VodV1DataHook implements HookRuntimeInterface
{
    public function handle(HookParams $params, \Closure $closure)
    {
        $block = $params->getValue('block');

        if (!in_array($block['sign'], [Constant::H5_BLOCK_SIGN_VOD_V1, Constant::PC_BLOCK_SIGN_VOD_V1])) {
            return $closure($params);
        }

        $params->setResponse($block);

        // 解析出首页装修板块配置的录播课的id
        $courseIds = collect($block['config_render']['items'])->pluck('id')->map(function ($val) {
            return (int)$val;
        })->toArray();

        if ($courseIds) {
            /**
             * @var CourseService $courseService
             */
            $courseService = app()->make(CourseServiceInterface::class);
            $courses = $courseService->getByIds(
                $courseIds,
                [
                    'id', 'title', 'thumb', 'charge', 'user_count', 'short_description',
                    'published_at', 'category_id', 'is_rec', 'is_free', 'created_at',
                ],
                ['category:id,name']
            );
            $courses = array_column($courses, null, 'id');

            foreach ($block['config_render']['items'] as $index => $courseItem) {
                $id = $courseItem['id'] ?? 0;
                // 前端渲染要考虑到无效的数据
                $block['config_render']['items'][$index] = $courses[$id] ?? null;
            }
            $params->setResponse($block);
        }

        return $closure($params);
    }
}

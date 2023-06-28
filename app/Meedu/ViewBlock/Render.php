<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ViewBlock;

use App\Constant\HookConstant;
use App\Meedu\Core\Hooks\HookRun;
use App\Meedu\Core\Hooks\HookParams;

class Render
{
    public static function dataRender(array $blocks): array
    {
        foreach ($blocks as $index => $blockItem) {
            if (in_array($blockItem['sign'], Constant::DATA_RENDER_BLOCK_WHITELIST)) {
                continue;
            }

            $tmpData = HookRun::run(HookConstant::VIEW_BLOCK_DATA_RENDER, new HookParams(['block' => $blockItem]));

            if ($tmpData) {
                // 如果渲染返回了数据则覆盖已有的数据
                $blocks[$index] = $tmpData;
            }
        }

        return $blocks;
    }
}

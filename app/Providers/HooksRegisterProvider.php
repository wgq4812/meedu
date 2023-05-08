<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Providers;

use App\Hooks\MpWechat\MessageReplyHook;
use App\Hooks\MpWechat\ScanEvtSubHook;
use App\Hooks\MpWechat\SubscribeHook;
use App\Hooks\ViewBlock\Data\VodV1DataHook;
use App\Meedu\Hooks\Constant\PositionConstant;
use App\Meedu\Hooks\HookContainer;
use Illuminate\Support\ServiceProvider;

class HooksRegisterProvider extends ServiceProvider
{
    protected $hooks = [
        PositionConstant::MP_WECHAT_RECEIVER_MESSAGE => [
            SubscribeHook::class,
            MessageReplyHook::class,
            ScanEvtSubHook::class,
        ],
        PositionConstant::VIEW_BLOCK_DATA_RENDER => [
            VodV1DataHook::class,
        ],
    ];

    public function boot()
    {
        foreach ($this->hooks as $position => $hooks) {
            HookContainer::getInstance()->register($position, $hooks);
        }
    }
}

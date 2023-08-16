<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\Wechat;

use App\Constant\HookConstant;
use App\Http\Controllers\Api\BaseController;
use App\Meedu\Core\Hooks\HookParams;
use App\Meedu\Core\Hooks\HookRun;
use App\Meedu\Utils\Wechat;

class MpWechatController extends BaseController
{
    public function serve()
    {
        $mp = Wechat::getInstance();
        $mp->server->push(function ($message) {
            return HookRun::run(HookConstant::MP_WECHAT_RECEIVER_MESSAGE, new HookParams([
                'MsgType' => $message['MsgType'] ?? '',
                'ToUserName' => $message['ToUserName'] ?? '',//openid
                'FromUserName' => $message['FromUserName'] ?? '',
                'CreateTime' => $message['CreateTime'] ?? '',
                'MsgId' => $message['MsgId'] ?? '',
                'raw' => $message,
            ]));
        });

        return $mp->server->serve();
    }
}

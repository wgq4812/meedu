<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use App\Meedu\Addons;
use App\Meedu\Cache\NavCache;
use App\Meedu\Cache\LinkCache;
use App\Meedu\Cache\ViewBlockH5IndexPageCache;
use App\Meedu\Cache\ViewBlockPCIndexPageCache;
use App\Http\Controllers\Api\V2\BaseController;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class SystemController extends BaseController
{
    public function status()
    {
        return $this->success();
    }

    public function config(
        Addons                    $addons,
        ConfigServiceInterface    $configService,
        LinkCache                 $linkCache,
        NavCache                  $navCache,
        ViewBlockPCIndexPageCache $PCIndexPageCache,
        ViewBlockH5IndexPageCache $h5IndexPageCache
    ) {
        $enabledAddons = $addons->enabledAddons();

        $playerBulletSecretConfig = $configService->getPlayerBulletSecret();

        $config = [
            // 网站名
            'webname' => $configService->getAppName(),
            // 网站地址
            'url' => trim($configService->getApiUrl(), '/'),
            'pc_url' => trim($configService->getPCUrl(), '/'),
            'h5_url' => trim($configService->getH5Url(), '/'),
            // ICP备案
            'icp' => $configService->getICP(),
            'icp_link' => $configService->getICPLink(),
            // 公安网备案
            'icp2' => $configService->getICP2(),
            'icp2_link' => $configService->getICP2Link(),
            // 用户协议URL
            'user_protocol' => route('user.protocol'),
            // 用户隐私协议URL
            'user_private_protocol' => route('user.private_protocol'),
            // 关于我们URL
            'aboutus' => route('aboutus'),
            // 网站logo
            'logo' => $configService->getLogo(),
            // 播放器配置
            'player' => [
                // 播放器封面
                'cover' => $configService->getPlayerCover(),
                // 跑马灯
                'enabled_bullet_secret' => $playerBulletSecretConfig['enabled'] ?? 0,
                'bullet_secret' => [
                    'size' => $playerBulletSecretConfig['size'] ?: 14,
                    'color' => $playerBulletSecretConfig['color'] ?: 'red',
                    'opacity' => $playerBulletSecretConfig['opacity'] ?: 1,
                    'text' => $playerBulletSecretConfig['text'] ?: '${mobile}',
                ],
            ],
            'member' => [
                // 强制绑定手机号
                'enabled_mobile_bind_alert' => $configService->enabledMustBindMobile(),
                // 强制实名认证
                'enabled_face_verify' => $configService->enabledFaceVerify(),
            ],
            // 社交登录
            'socialites' => [
                'qq' => $configService->enabledQQLogin(),
                'wechat_scan' => $configService->enabledWechatScanLogin(),
                'wechat_oauth' => $configService->enabledWechatOAUTHLogin(),
            ],
            // 积分奖励
            'credit1_reward' => [
                // 注册
                'register' => $configService->getCredit1Register(),
                // 看完录播课
                'watched_vod_course' => $configService->getCredit1WatchedCourse(),
                // 看完视频
                'watched_video' => $configService->getCredit1WatchedVideo(),
                // 已支付订单[抽成]
                'paid_order' => $configService->getCredit1CreatedPaidOrder(),
            ],
            // 已用插件
            'enabled_addons' => $enabledAddons,
            // 已开启的支付网关
            'payments' => $configService->enabledPayments(),
        ];

        return $this->data([
            'config' => $config,
            'navs' => $navCache->get(),
            'links' => $linkCache->get(),
            'view_block_pc_index_page' => $PCIndexPageCache->get(),
            'view_block_h5_index_page' => $h5IndexPageCache->get(),
        ]);
    }
}

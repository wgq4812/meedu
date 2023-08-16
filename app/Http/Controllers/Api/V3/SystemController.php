<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use App\Meedu\Core\Addons;
use App\Meedu\Cache\NavCache;
use App\Meedu\Cache\LinkCache;
use App\Http\Controllers\Api\BaseController;
use App\Meedu\Cache\ViewBlockH5IndexPageCache;
use App\Meedu\Cache\ViewBlockPCIndexPageCache;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class SystemController extends BaseController
{
    /**
     * @api {GET} /api/v3/status 系统状态
     * @apiGroup System
     * @apiName  SystemStatus
     * @apiVersion v3.0.0
     * @apiDescription v5.0 新增
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     */
    public function status()
    {
        return $this->success();
    }

    /**
     * @api {GET} /api/v3/config 系统配置
     * @apiGroup System
     * @apiName  SystemConfig
     * @apiVersion v3.0.0
     * @apiDescription v5.0 新增
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.name 网站名
     * @apiSuccess {String} data.url API地址
     * @apiSuccess {String} data.pc_url PC地址
     * @apiSuccess {String} data.h5_url H5地址
     * @apiSuccess {String} data.go_meedu_url GoMeEdu地址
     * @apiSuccess {String} data.icp ICP备案号
     * @apiSuccess {String} data.icp_link ICP备案号查询地址
     * @apiSuccess {String} data.icp2 公安网络备案号
     * @apiSuccess {String} data.icp2_link 公安网络备案查询地址
     * @apiSuccess {String} data.user_protocol 用户协议URL
     * @apiSuccess {String} data.user_private_protocol 用户隐私协议URL
     * @apiSuccess {String} data.aboutus 关于我们
     * @apiSuccess {String} data.logo 网站Logo
     * @apiSuccess {String[]} data.enabled_addons 启动的插件
     * @apiSuccess {String[]} data.payments 启动的支付网关
     * @apiSuccess {Object} data.player 播放器配置
     * @apiSuccess {String} data.player.cover 播放器封面
     * @apiSuccess {Number} data.player.enabled_bullet_secret 是否启用跑马灯
     * @apiSuccess {Object} data.player.bullet_secret 跑马灯配置
     * @apiSuccess {String} data.player.bullet_secret.size 文字大小
     * @apiSuccess {String} data.player.bullet_secret.color 文字颜色
     * @apiSuccess {String} data.player.bullet_secret.opacity 文字透明度
     * @apiSuccess {String} data.player.bullet_secret.text 跑马灯文本
     * @apiSuccess {Object} data.member 学员配置
     * @apiSuccess {Boolean} data.member.enabled_mobile_bind_alert 是否强制绑定手机号
     * @apiSuccess {Boolean} data.member.enabled_face_verify 是否强制实名认证
     * @apiSuccess {Object} data.socialites 社交登录配置
     * @apiSuccess {Boolean} data.socialites.qq 是否开启QQ登录
     * @apiSuccess {Boolean} data.socialites.wechat_scan 是否开启微信扫码登录
     * @apiSuccess {Boolean} data.socialites.wechat_oauth 是否开启微信授权登录
     * @apiSuccess {Object} data.credit1_reward 积分配置
     * @apiSuccess {String} data.credit1_reward.register 注册送N积分
     * @apiSuccess {String} data.credit1_reward.watched_vod_course 看完课程送N积分
     * @apiSuccess {String} data.credit1_reward.watched_video 看完视频送N积分
     * @apiSuccess {String} data.credit1_reward.paid_order 支付订单送积分
     */
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
            'name' => $configService->getAppName(),
            // 网站地址
            'url' => trim($configService->getApiUrl(), '/'),
            'pc_url' => trim($configService->getPCUrl(), '/'),
            'h5_url' => trim($configService->getH5Url(), '/'),
            'go_meedu_url' => trim($configService->getH5Url(), '/'),
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
            // 支付网关状态
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

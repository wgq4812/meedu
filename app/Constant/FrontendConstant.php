<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Constant;

class FrontendConstant
{
    const PAYMENT_PLATFORMS = [
        self::PAYMENT_PLATFORM_PC,
        self::PAYMENT_PLATFORM_H5,
        self::PAYMENT_PLATFORM_WECHAT,
        self::PAYMENT_PLATFORM_WECHAT_MINI,
        self::PAYMENT_PLATFORM_APP,
    ];
    const PAYMENT_PLATFORM_PC = 'pc';
    const PAYMENT_PLATFORM_H5 = 'h5';
    const PAYMENT_PLATFORM_WECHAT = 'wechat';
    const PAYMENT_PLATFORM_WECHAT_MINI = 'wechat-mini';
    const PAYMENT_PLATFORM_APP = 'app';

    const PAYMENT_SCENE_PC = 'pc';
    const PAYMENT_SCENE_H5 = 'h5';
    const PAYMENT_SCENE_WECHAT = 'wechat';
    const PAYMENT_SCENE_HAND_PAY = 'handPay';

    // 支付渠道-方法
    const PAYMENT_SCENE_WECHAT_SCAN = 'scan';
    const PAYMENT_SCENE_WECHAT_MINI = 'miniapp';
    const PAYMENT_CHANNEL_ALI_SCAN = 'ali-scan';

    const ORDER_UN_PAY = 1;
    const ORDER_PAYING = 5;
    const ORDER_CANCELED = 7;
    const ORDER_PAID = 9;

    // api会员认证driver
    const API_GUARD = 'apiv2';

    // 登录平台
    public const LOGIN_PLATFORM_PC = 'PC';
    public const LOGIN_PLATFORM_H5 = 'H5';
    public const LOGIN_PLATFORM_IOS = 'IOS';
    public const LOGIN_PLATFORM_ANDROID = 'ANDROID';
    public const LOGIN_PLATFORM_MINI = 'MINI';
    public const LOGIN_PLATFORM_APP = 'APP';
    public const LOGIN_PLATFORM_OTHER = 'OTHER';

    public const USER_LOGIN_AT_COOKIE_NAME = 'last_login_at';

    // 不限制
    public const LOGIN_LIMIT_RULE_DEFAULT = 1;
    // 所有平台只允许一台设备已登录
    public const LOGIN_LIMIT_RULE_ALL = 3;

    // 幻灯片设备
    public const SLIDER_PLATFORM_PC = 'PC';
    public const SLIDER_PLATFORM_H5 = 'H5';
    public const SLIDER_PLATFORM_MINI = 'MINI';
    public const SLIDER_PLATFORM_APP = 'APP';

    // 导航栏平台
    public const NAV_PLATFORM_PC = 'PC';
    public const NAV_PLATFORM_H5 = 'h5';

    // 微信小程序登录sign
    public const WECHAT_MINI_LOGIN_SIGN = 'WECHAT-MINI';

    // 微信公众号登录sign
    public const WECHAT_LOGIN_SIGN = 'wechat';

    // 手机号强制绑定路由检测白名单
    public const MOBILE_BIND_ROUTE_WHITELIST = [
        'member.mobile.bind',
        'member.mobile.bind.submit',
        'logout',
    ];

    public const LANG_ZH = 'zh';
    public const LANG_EN = 'en';

    public const SOCIALITE_APP_QQ = 'qq';

    // 点播服务
    public const VOD_SERVICE_TENCENT = 'tencent';
    public const VOD_SERVICE_ALIYUN = 'aliyun';

    // 实名认证最佳帧图片存储路径
    public const USER_VERIFY_FACE_IMAGE_SAVE_PATH = 'meedu/userFaceVerify/images';
    // 实名认证视频存储路径
    public const USER_VERIFY_FACE_VIDEO_SAVE_PATH = 'meedu/userFaceVerify/videos';
    public const USER_VERIFY_FACE_ID_CARD_SAVE_PATH = '/meedu/userFaceVerify/idCard';
}

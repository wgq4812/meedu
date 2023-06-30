<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\QQ\\QqExtendSocialite@handle',
        ],
        // 支付成功event
        'App\Events\PaymentSuccessEvent' => [
            '\App\Listeners\PaymentSuccessEvent\DeliverListener',
            '\App\Listeners\PaymentSuccessEvent\NotificationListener',
            '\App\Listeners\PaymentSuccessEvent\StatusChangeListener',
            '\App\Listeners\PaymentSuccessEvent\Credit1RewardListener',
        ],
        // 订单取消
        'App\Events\OrderCancelEvent' => [
            'App\Listeners\OrderCancelEvent\OrderGoodsCancelHandlerListener',
        ],
        // 学员注册
        'App\Events\UserRegisterEvent' => [
            'App\Listeners\UserRegisterEvent\WelcomeMessageListener',
            'App\Listeners\UserRegisterEvent\RegisterIpRecordListener',
            'App\Listeners\UserRegisterEvent\RegisterCredit1RewardListener',
            'App\Listeners\UserRegisterEvent\RegisterSendVipListener',
        ],
        // 学员登录
        'App\Events\UserLoginEvent' => [
            'App\Listeners\UserLoginEvent\LoginRecordListener',
            'App\Listeners\UserLoginEvent\UserDeleteCancelListener',
        ],
        // 学员退出登录
        'App\Events\UserLogoutEvent' => [
            'App\Listeners\UserLogoutEvent\LoginRecordUpdateListener',
        ],
        // 学员看完视频
        'App\Events\UserVideoWatchedEvent' => [
            'App\Listeners\UserVideoWatchedEvent\UserVideoWatchedListener',
            'App\Listeners\UserVideoWatchedEvent\UserVideoWatchedCredit1RewardListener',
        ],
        // 学员看完录播课程
        'App\Events\UserCourseWatchedEvent' => [
            'App\Listeners\UserCourseWatchedEvent\UserCourseWatchedListener',
            'App\Listeners\UserCourseWatchedEvent\UserCourseWatchedCredit1RewardListener',
        ],
        // 系统配置变更
        'App\Events\AppConfigSavedEvent' => [],
        // 录播课程的增改删
        'App\Events\VodCourseCreatedEvent' => [
            'App\Listeners\VodCourseCreatedEvent\SearchRecordNotify',
        ],
        'App\Events\VodCourseUpdatedEvent' => [
            'App\Listeners\VodCourseUpdatedEvent\SearchRecordNotify',
        ],
        'App\Events\VodCourseDestroyedEvent' => [
            'App\Listeners\VodCourseDestroyedEvent\SearchRecordNotify',
        ],
        // 录播视频的增改删
        'App\Events\VodVideoCreatedEvent' => [
            'App\Listeners\VodVideoCreatedEvent\SearchRecordNotify',
        ],
        'App\Events\VodVideoUpdatedEvent' => [
            'App\Listeners\VodVideoUpdatedEvent\SearchRecordNotify',
        ],
        'App\Events\VodVideoDestroyedEvent' => [
            'App\Listeners\VodVideoDestroyedEvent\SearchRecordNotify',
            'App\Listeners\VodVideoDestroyedEvent\UserWatchedRecordClear',
        ],
        // 新视频上传event
        'App\Events\VideoUploadedEvent' => [],
        // 视频转码完成event
        'App\Events\VideoTranscodeCompleteEvent' => [],
        // 退款已申请
        'App\Events\OrderRefundCreated' => [],
        // 退款已处理[不一定成功]
        'App\Events\OrderRefundProcessed' => [
            'App\Listeners\OrderRefundProcessed\StatusChangeListener',
            'App\Listeners\OrderRefundProcessed\UserNotifyListener',
        ],
        // 学员删除-申请
        'App\Events\UserDeleteSubmitEvent' => [],
        // 学员删除-取消
        'App\Events\UserDeleteCancelEvent' => [
            'App\Listeners\UserDeleteCancelEvent\UserNotify',
        ],
        // 学员删除-确认
        'App\Events\UserDeletedEvent' => [],
        // 学员实名认证通过
        'App\Events\UserVerifyFaceSuccessEvent' => [
            'App\Listeners\UserVerifyFaceSuccessEvent\UserNotifyListener',
            'App\Listeners\UserVerifyFaceSuccessEvent\UserProfileUpdateListener',
        ],
        // 阿里云点播回调
        'App\Events\AliVodCallbackEvent' => [
            'App\Listeners\AliVodCallbackEvent\VideoCreatedListener',
            'App\Listeners\AliVodCallbackEvent\DestroyListener',
        ],
        // 腾讯云点播回调
        'App\Events\TencentVodCallbackEvent' => [
            'App\Listeners\TencentVodCallbackEvent\VideoCreatedListener',
            'App\Listeners\TencentVodCallbackEvent\DestroyListener',
        ],
        // 导航栏更新
        'App\Events\NavUpdateEvent' => [
            'App\Listeners\NavUpdateEvent\CacheClearListener',
        ],
        // 友情链接更新
        'App\Events\LinkUpdateEvent' => [
            'App\Listeners\LinkUpdateEvent\CacheClearListener',
        ],
        // 首页装修模块更新
        'App\Events\ViewBlockUpdateEvent' => [
            'App\Listeners\ViewBlockUpdateEvent\CacheClearListener',
        ],
        // 录播课分类更新
        'App\Events\CourseCategoryUpdateEvent' => [
            'App\Listeners\CourseCategoryUpdateEvent\CacheClearListener',
        ],
    ];
}

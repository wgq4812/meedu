<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

// 内容给你搜索
Route::get('/search', 'SearchController@index');
// 系统状态查询[可以用于负载均衡的健康查询]
Route::get('/status', 'SystemController@status');
// 系统配置
Route::get('/config', 'SystemController@config');

Route::group(['prefix' => 'captcha'], function () {
    Route::post('/sms', 'CaptchaController@sms');
    Route::get('/image', 'CaptchaController@image');
});

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('/password-reset', 'PasswordController@reset');

    Route::group([
        'prefix' => 'register',
    ], function () {
        // 短信注册
        Route::post('/sms', 'RegisterController@sms');
        // 注册-社交登录
        Route::post('/socialite', 'RegisterController@socialite');
        // 注册-微信公众号扫码
        Route::post('/wechat-scan', 'RegisterController@wechatScan');
    });

    Route::group([
        'prefix' => 'login',
    ], function () {
        // 密码登录
        Route::post('/password', 'LoginController@password');
        // 短信登录
        Route::post('/sms', 'LoginController@sms');
        // 微信公众号授权登录
        Route::get('/wechat/oauth', 'LoginController@wechatOauthLogin');
        // 微信公众号授权登录-返回
        Route::get('/wechat/oauth/callback', 'LoginController@wechatOauthCallback')->name('api.v3.login.wechat.callback');
        // 社交登录
        Route::get('/socialite/{app}', 'LoginController@socialiteLogin');
        // 社交登录返回
        Route::get('/socialite/{app}/callback', 'LoginController@socialiteLoginCallback')->name('api.v3.login.socialite.callback');
        // 微信公众号扫码登录
        Route::get('/wechat/scan', 'LoginController@wechatScan');
        // 微信公众号扫码登录-结果查询
        Route::get('/wechat/scan/query', 'LoginController@wechatScanQuery');
        // 通过code登录系统[code由社交登录、微信扫码登录发放]
        Route::post('/code', 'LoginController@loginByCode');
    });
});

// 录播课分页列表
Route::get('/courses', 'CourseController@index');
// 课程附件下载
Route::get('/course/{courseId}/attach/{id}/download', 'CourseAttachController@download')->name('course.attachment.download');

// VIP
Route::get('/roles', 'RoleController@index');

// 公告
Route::get('/announcements', 'AnnouncementController@index');
Route::get('/announcement/{slug}', 'AnnouncementController@detail');

Route::group(['middleware' => ['auth:apiv2', 'api.login.status.check']], function () {
    // 安全退出
    Route::post('/auth/logout', 'LogoutController@logout');

    Route::group(['prefix' => 'order'], function () {
        // 创建订单
        Route::post('/', 'OrderController@store');
        Route::get('/status', 'PaymentController@status');
        Route::get('/promoCode', 'PaymentController@promoCode');
        // 订单支付
        Route::post('/pay', 'PaymentController@submit');
    });

    Route::group(['prefix' => 'course'], function () {
        // 视频播放地址
        Route::post('/{courseId}/video/{videoId}/play', 'VideoController@play');
        Route::post('/{courseId}/attach/{id}/download-url', 'CourseAttachController@getDownloadUrl');
    });

    Route::group(['prefix' => 'member'], function () {
        // 学员已购录播课
        Route::get('/courses', 'MemberController@courses');
        // 学员的全部已学习录播课
        Route::get('/courses/learned', 'MemberController@learnedCourses');
        // 学员某个课程的学习明细(课程的所有课时观看进度)
        Route::get('/learned/course/{courseId}', 'MemberController@learnedCourseDetail');
        // 学员喜欢的课程
        Route::get('/courses/like', 'MemberController@likeCourses');

        // 账户注销
        Route::post('/destroy', 'MemberController@destroy');
        // 社交登录绑定
        Route::post('/socialite/bind-with-code', 'MemberController@socialiteBindByCode');
        // 微信账号扫码绑定
        Route::get('/wechat-scan-bind', 'MemberController@wechatScanBind');

        // 微信实人认证结果查询
        Route::get('/tencent/faceVerify', 'MemberController@queryTencentFaceVerify');
        // 请求发起微信实人认证
        Route::post('/tencent/faceVerify', 'MemberController@tencentFaceVerify');
    });
});

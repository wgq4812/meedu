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

// 微信公众号授权登录
Route::get('/auth/login/wechat/oauth', 'LoginController@wechatOauthLogin');
// 微信公众号授权登录-返回
Route::get('/auth/login/wechat/callback', 'LoginController@wechatOauthCallback')->name('api.v3.login.wechat.callback');
// 社交登录
Route::get('/auth/login/socialite/{app}', 'LoginController@socialiteLogin');
// 社交登录返回
Route::get('/auth/login/socialite/{app}/callback', 'LoginController@socialiteLoginCallback')->name('api.v3.login.socialite.callback');
// 微信公众号扫码登录
Route::get('/auth/login/wechat/scan', 'LoginController@wechatScan');
// 微信公众号扫码登录-结果查询
Route::get('/auth/login/wechat/scan/query', 'LoginController@wechatScanQuery');

// 通过code登录系统[code由社交登录、微信扫码登录发放]
Route::post('/auth/login/code', 'LoginController@loginByCode');
// 注册-社交登录
Route::post('/auth/register/withSocialite', 'LoginController@registerWithSocialite');
// 注册-微信公众号扫码
Route::post('/auth/register/withWechatScan', 'LoginController@registerWithWechatScan');

// 录播课分页列表
Route::get('/courses', 'CourseController@index');

Route::group(['middleware' => ['auth:apiv2', 'api.login.status.check']], function () {
    // 创建订单
    Route::post('/order', 'PaymentController@create');
    // 订单支付
    Route::post('/order/pay', 'PaymentController@submit');

    // 视频播放地址
    Route::post('/course/{courseId}/video/{videoId}/play', 'VideoController@play');

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
        Route::post('/socialite/bindWithCode', 'MemberController@socialiteBindByCode');
        // 微信账号扫码绑定
        Route::get('/wechatScanBind', 'MemberController@wechatScanBind');

        // 微信实人认证结果查询
        Route::get('/tencent/faceVerify', 'MemberController@queryTencentFaceVerify');
        // 请求发起微信实人认证
        Route::post('/tencent/faceVerify', 'MemberController@tencentFaceVerify');
    });
});

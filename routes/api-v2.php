<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

Route::get('/captcha/image', 'CaptchaController@imageCaptcha');
// 发送手机验证码
Route::post('/captcha/sms', 'CaptchaController@sentSms');
// 手机短信注册
Route::post('/register/sms', 'RegisterController@smsHandler');
// 密码重置
Route::post('/password/reset', 'PasswordController@reset');
// 密码登录
Route::post('/login/password', 'LoginController@passwordLogin');
// 手机号登录
Route::post('/login/mobile', 'LoginController@mobileLogin');

// 录播课-列表
Route::get('/courses', 'CourseController@paginate');
// 录播课-详情
Route::get('/course/{id}', 'CourseController@detail');
// 录播课-评论-列表
Route::get('/course/{id}/comments', 'CourseController@comments');
// 全部课程分类
Route::get('/course_categories', 'CourseCategoryController@all');
// 录播课-课时-详情
Route::get('/video/{id}', 'VideoController@detail');
// 公开的视频播放
Route::get('/video/open/play', 'VideoController@openPlay');

Route::group(['middleware' => ['auth:apiv2', 'api.login.status.check']], function () {
    // 录播课-学员学习-记录
    Route::post('/video/{id}/record', 'VideoController@recordVideo');
    // 录播课-课时-获取播放地址
    Route::get('/video/{id}/playinfo', 'VideoController@playInfo');
    // 安全退出
    Route::post('/logout', 'LoginController@logout');
    // 录播课-评论
    Route::post('/course/{id}/comment', 'CourseController@createComment');
    // 录播课-收藏
    Route::get('/course/{id}/like', 'CourseController@like');

    // 图片上传
    Route::post('/upload/image', 'UploadController@image');

    Route::group(['prefix' => 'member'], function () {
        // 用户详情
        Route::get('detail', 'MemberController@detail');
        // 密码修改
        Route::post('detail/password', 'MemberController@passwordChange');
        // 头像修改
        Route::post('detail/avatar', 'MemberController@avatarChange');
        // 昵称修改
        Route::post('detail/nickname', 'MemberController@nicknameChange');
        // 手机号绑定[未绑定情况下]
        Route::post('detail/mobile', 'MemberController@mobileBind');
        // 更换手机号
        Route::put('mobile', 'MemberController@mobileChange');
        // 我的录播课
        Route::get('courses', 'MemberController@courses');
        // 录播课程收藏
        Route::get('courses/like', 'MemberController@likeCourses');
        // 录播课程学习历史
        Route::get('courses/history', 'MemberController@learnHistory');
        // 我的录播视频
        Route::get('videos', 'MemberController@videos');
        // 我的订单
        Route::get('orders', 'MemberController@orders');
        // 我的VIP记录
        Route::get('roles', 'MemberController@roles');
        // 我的消息
        Route::get('messages', 'MemberController@messages');
        // 消息已读
        Route::get('notificationMarkAsRead/{notificationId}', 'MemberController@notificationMarkAsRead');
        // 消息全部已读
        Route::get('notificationMarkAllAsRead', 'MemberController@notificationMarkAllAsRead');
        // 未读消息数量
        Route::get('unreadNotificationCount', 'MemberController@unreadNotificationCount');
        // 积分明细
        Route::get('credit1Records', 'MemberController@credit1Records');
        // 安全校验[手机号]
        Route::post('verify', 'MemberController@verify');
        // 社交账号取消绑定
        Route::delete('socialite/{app}', 'MemberController@socialiteCancelBind');
    });
});

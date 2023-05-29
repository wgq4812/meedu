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
// 微信公众号扫码登录-创建二维码
Route::get('/login/wechatScan', 'LoginController@wechatScan');
// 微信公众号扫码登录-结果查询
Route::get('/login/wechatScan/query', 'LoginController@wechatScanQuery');

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

// VIP-列表
Route::get('/roles', 'RoleController@roles');
// VIP-详情
Route::get('/role/{id}', 'RoleController@detail');

// 幻灯片
Route::get('/sliders', 'SliderController@all');
// 友情链接
Route::get('/links', 'LinkController@all');
// 首页导航
Route::get('/navs', 'NavController@all');

// 公告-最新一条
Route::get('/announcement/latest', 'AnnouncementController@latest');
// 公告-列表
Route::get('/announcements', 'AnnouncementController@list');
// 公告-详情
Route::get('/announcement/{id}', 'AnnouncementController@detail');

// 优惠码检测
Route::get('/promoCode/{code}', 'PromoCodeController@detail');
// 系统常用配置
Route::get('/other/config', 'OtherController@config');
// 首页装修模块-列表
Route::get('/viewBlock/page/blocks', 'ViewBlockController@pageBlocks');

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
    // 录播课-附件下载
    Route::get('/course/attach/{id}/download', 'CourseController@attachDownload');

    // 录播课-创建购买订单
    Route::post('/order/course', 'OrderController@createCourseOrder');
    // VIP-创建购买订单
    Route::post('/order/role', 'OrderController@createRoleOrder');
    // 订单是否支付状态查询
    Route::get('/order/status', 'OrderController@queryStatus');

    // 跳转到第三方平台支付[如：支付宝web支付]
    Route::get('/order/pay/redirect', 'PaymentController@payRedirect');
    // 手动打款支付
    Route::get('/order/pay/handPay', 'PaymentController@handPay');
    // 微信扫码支付
    Route::post('/order/pay/wechatScan', 'PaymentController@wechatScan');
    // 获取可用支付网关
    Route::get('/order/payments', 'PaymentController@payments');

    // 优惠码-检测是否可用
    Route::get('/promoCode/{code}/check', 'PromoCodeController@checkCode');

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
        // 微信扫码登录绑定
        Route::get('wechatScan/bind', 'MemberController@wechatScanBind');
        // 社交账号取消绑定
        Route::delete('socialite/{app}', 'MemberController@socialiteCancelBind');
    });
});


// 不再推荐继续使用的api
// MeEdu-v5版本开始新安装系统默认关闭不推荐api的访问，在将来某个版本将会删除这些api
// 可以通过编辑 .env 文件中的 CLOSE_DEPRECATED_API=true 继续使用这些api
Route::group(['middleware' => ['deprecated.api']], function () {
    // 微信公众号授权登录
    Route::get('/login/wechat/oauth', 'LoginController@wechatLogin');
    Route::get('/login/wechat/oauth/callback', 'LoginController@wechatLoginCallback')->name('api.v2.login.wechat.callback');
    // 微信公众号授权绑定回调
    Route::get('wechatBind/callback', 'MemberController@wechatBindCallback')->name('api.v2.wechatBind.callback');
    // 社交登录
    Route::get('/login/socialite/{app}', 'LoginController@socialiteLogin');
    Route::get('/login/socialite/{app}/callback', 'LoginController@socialiteLoginCallback')->name('api.v2.login.socialite.callback');
    // 社交账号绑定回调
    Route::get('socialite/{app}/bind/callback', 'MemberController@socialiteBindCallback')->name('api.v2.socialite.bind.callback');
    // 课程搜索
    Route::get('/search', 'SearchController@index');
    // 录播课-课时-列表
    Route::get('/videos', 'VideoController@paginate');
    // 录播课-课时-评论列表
    Route::get('/video/{id}/comments', 'VideoController@comments');

    Route::group(['middleware' => ['auth:apiv2', 'api.login.status.check']], function () {
        // 录播课-课时-创建评论
        Route::post('/video/{id}/comment', 'VideoController@createComment');
        // 社交账号绑定
        Route::get('/member/socialite/{app}', 'MemberController@socialiteBind');
        // 微信公众号授权绑定
        Route::get('/member/wechatBind', 'MemberController@wechatBind');
    });
});

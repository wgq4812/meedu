<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

Route::group([
    'middleware' => [
        'auth:administrator',
        \App\Http\Middleware\Backend\SystemBaseConfigCheckMiddleware::class,
    ],
], function () {
    Route::get('/system/config', 'SystemController@config');
});

Route::group([
    'middleware' => [
        'auth:administrator',
        \App\Http\Middleware\Backend\SystemBaseConfigCheckMiddleware::class,
        'backend.permission',
    ],
], function () {
    Route::group(['prefix' => 'member'], function () {
        Route::get('/courses', 'MemberController@courses');
        Route::get('/course/progress', 'MemberController@courseProgress');
        Route::get('/videos', 'MemberController@videos');

        Route::delete('/{id}', 'MemberController@destroy');
    });

    Route::group(['prefix' => 'stats'], function () {
        Route::get('/transaction', 'StatsController@transaction');
        Route::get('/transaction-top', 'StatsController@transactionTop');
        Route::get('/transaction-graph', 'StatsController@transactionGraph');

        Route::get('/user-paid-top', 'StatsController@userPaidTop');
        Route::get('/user', 'StatsController@user');
        Route::get('/user-graph', 'StatsController@userGraph');
    });

    Route::group(['prefix' => 'tencent-vod'], function () {
        // todo - 权限
        Route::get('/check', 'TencentVodController@check');

        Route::get('/app/index', 'TencentVodController@apps');
        Route::post('/app/create', 'TencentVodController@appConfirm');

        Route::get('/domain/index', 'TencentVodController@domains');
        Route::post('/domain/create', 'TencentVodController@domainSwitch');
        Route::post('/domain/key', 'TencentVodController@domainKeyReset');

        Route::post('/cdn/key', 'TencentVodController@saveCdnKey');
    });

    Route::group(['prefix' => 'ali-vod'], function () {
        // todo - 权限
        Route::get('/check', 'AliVodController@check');
    });
});

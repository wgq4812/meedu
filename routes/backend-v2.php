<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

Route::group([
    'middleware' => ['auth:administrator'],
], function () {
    Route::get('/system/config', 'SystemController@config');
});

Route::group([
    'middleware' => ['auth:administrator', 'backend.permission'],
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

    Route::group(['prefix' => 'runtime'], function () {
        Route::group(['prefix' => 'tencent-vod'], function () {
            Route::get('/check', 'TencentVodController@check');

            Route::get('/app/index', 'TencentVodController@apps');
            Route::post('/app/create', 'TencentVodController@appConfirm');

            Route::get('/domain/index', 'TencentVodController@domains');
            Route::post('/domain/create', 'TencentVodController@domainSwitch');
            Route::post('/domain/key', 'TencentVodController@domainKeyReset');

            Route::post('/transcode-submit', 'TencentVodController@transcodeSubmit');
            Route::post('/transcode-destroy', 'TencentVodController@transcodeDestroy');
        });

        Route::group(['prefix' => 'ali-vod'], function () {
            Route::get('/check', 'AliVodController@check');

            Route::get('/transcode-config', 'AliVodController@transcodeConfig');
            Route::post('/transcode-submit', 'AliVodController@transcodeSubmit');
            Route::post('/transcode-destroy', 'AliVodController@transcodeDestroy');
        });
    });
});

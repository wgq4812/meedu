<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Constant;

class RuntimeConstant
{
    public const STATUS_OK = 'ok';

    public const SYSTEM_SCHEDULE = 'system-schedule';

    // 腾讯云-点播配置
    public const TENCENT_VOD_NAMES = [
        self::TENCENT_VOD_SECRET,
        self::TENCENT_VOD_APP,
        self::TENCENT_VOD_EVENT,
        self::TENCENT_VOD_DOMAIN,
        self::TENCENT_VOD_TRANSCODE_TASK_SIMPLE,
        self::TENCENT_VOD_DOMAIN_KEY,
        self::TENCENT_VOD_CDN_KEY,
    ];
    public const TENCENT_VOD_SECRET = 'tencent-vod-secret';
    public const TENCENT_VOD_APP = 'tencent-vod-app';
    public const TENCENT_VOD_EVENT = 'tencent-vod-event';
    public const TENCENT_VOD_DOMAIN = 'tencent-vod-domain';
    public const TENCENT_VOD_TRANSCODE_TASK_SIMPLE = 'tencent-vod-transcode-task-simple';
    public const TENCENT_VOD_DOMAIN_KEY = 'tencent-vod-domain-key';
    public const TENCENT_VOD_CDN_KEY = 'tencent-cdn-key';

    // 阿里云点播配置
    public const ALI_VOD_NAMES = [
        self::ALI_VOD_SECRET,
        self::ALI_VOD_APP,
        self::ALI_VOD_DOMAIN,
        self::ALI_VOD_EVENT,
        self::ALI_VOD_TRANSCODE,
    ];
    public const ALI_VOD_SECRET = 'ali-vod-secret';
    public const ALI_VOD_APP = 'ali-vod-app';
    public const ALI_VOD_EVENT = 'ali-vod-event';
    public const ALI_VOD_DOMAIN = 'ali-vod-domain';
    public const ALI_VOD_TRANSCODE = 'ali-vod-transcode';
}

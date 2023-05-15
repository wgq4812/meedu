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

    public const SYSTEM_VERSION = 'system-version';
    public const SYSTEM_SCHEDULE = 'system-schedule';
    public const SYSTEM_QUEUE = 'system-queue';

    public const TENCENT_VOD_NAMES = [
        self::TENCENT_VOD_SECRET,
        self::TENCENT_VOD_APP,
        self::TENCENT_VOD_EVENT,
        self::TENCENT_VOD_DOMAIN,
        self::TENCENT_VOD_TRANSCODE_TASK_SIMPLE,
        self::TENCENT_VOD_DOMAIN_KEY,
    ];

    public const TENCENT_VOD_SECRET = 'tencent-vod-secret';
    public const TENCENT_VOD_APP = 'tencent-vod-app';
    public const TENCENT_VOD_EVENT = 'tencent-vod-event';
    public const TENCENT_VOD_DOMAIN = 'tencent-vod-domain';
    public const TENCENT_VOD_TRANSCODE_TASK_SIMPLE = 'tencent-vod-transcode-task-simple';
    public const TENCENT_VOD_DOMAIN_KEY = 'tencent-vod-domain-key';
}

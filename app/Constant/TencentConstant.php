<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Constant;

class TencentConstant
{
    public const VOD_TRANSCODE_SIMPLE_TASK = 'MeEduSimple';

    public const VOD_TRANSCODE_ADAPTIVE = 'SimpleAesEncryptPreset';

    public const VOD_TRANSCODE_NAMES = [
        self::VOD_TRANSCODE_SIMPLE_TASK,
        self::VOD_TRANSCODE_ADAPTIVE,
    ];

    public const VOD_DELETE_PART_TRANSCODE = 'TranscodeFiles';

    public const VOD_DELETE_PART_ADAPTIVE = 'AdaptiveDynamicStreamingFiles';

    public const VOD_TRANSCODE_2_DELETE_PART = [
        self::VOD_TRANSCODE_SIMPLE_TASK => self::VOD_DELETE_PART_TRANSCODE,
        self::VOD_TRANSCODE_ADAPTIVE => self::VOD_DELETE_PART_ADAPTIVE,
    ];
}

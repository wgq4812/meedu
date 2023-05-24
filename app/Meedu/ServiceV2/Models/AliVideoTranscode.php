<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Models;

use Illuminate\Database\Eloquent\Model;

class AliVideoTranscode extends Model
{
    protected $table = 'ali_video_transcode';

    protected $fillable = [
        'file_id', 'template_id', 'template_name', 'status',
    ];
}

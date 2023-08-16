<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Models;

use Illuminate\Database\Eloquent\Model;

class RuntimeStatus extends Model
{
    protected $table = 'runtime_status';

    protected $fillable = [
        'name', 'status',
    ];
}

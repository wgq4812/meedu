<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Models;

use Illuminate\Database\Eloquent\Model;

class SmsRecord extends Model
{

    protected $table = 'sms_records';

    protected $fillable = [
        'mobile', 'send_data', 'response_data',
    ];

}

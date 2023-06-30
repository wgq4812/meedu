<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPaidRecord extends Model
{
    protected $table = 'order_paid_records';

    protected $fillable = [
        'user_id', 'order_id', 'paid_total', 'paid_type', 'paid_type_id',
    ];
}

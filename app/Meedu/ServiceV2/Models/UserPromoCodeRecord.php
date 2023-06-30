<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPromoCodeRecord extends Model
{
    use SoftDeletes;

    protected $table = 'user_promo_code_records';

    protected $fillable = [
        'user_id', 'code_id', 'order_id', 'original_amount', 'discount', 'created_at',
    ];

    public $timestamps = false;
}

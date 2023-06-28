<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V2\BaseController;

class OrderController extends BaseController
{
    public function create(Request $request)
    {
        $type = $request->input('type');
        $id = (int)$request->input('id');
        if (!$type || !$id) {
            return $this->error(__('参数错误'));
        }
        return $this->data();
    }
}

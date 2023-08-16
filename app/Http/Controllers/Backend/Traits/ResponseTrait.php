<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Backend\Traits;

use Illuminate\Support\Facades\Auth;

trait ResponseTrait
{
    public function adminId()
    {
        return Auth::guard('administrator')->id();
    }

    protected function success($message = '')
    {
        return response()->json([
            'status' => 0,
            'message' => $message,
            'data' => [],
        ]);
    }

    protected function successData($data = [], $message = '')
    {
        return response()->json([
            'status' => 0,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function error($message, $code = 1)
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => [],
        ]);
    }
}

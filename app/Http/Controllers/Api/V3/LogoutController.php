<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use App\Events\UserLogoutEvent;
use App\Constant\FrontendConstant;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BaseController;

class LogoutController extends BaseController
{

    /**
     * @api {post} /api/v3/auth/logout 安全退出
     * @apiGroup Auth-V3
     * @apiName V3-Logout
     * @apiVersion v3.0.0
     *
     * @apiSuccess {Number} code 0成功,非0失败
     */
    public function logout(Request $request)
    {
        $userId = Auth::guard(FrontendConstant::API_GUARD)->id();

        $token = explode(' ', $request->header('Authorization'))[1];

        event(new UserLogoutEvent($userId, $token));

        Auth::guard(FrontendConstant::API_GUARD)->logout();

        return $this->success();
    }

}

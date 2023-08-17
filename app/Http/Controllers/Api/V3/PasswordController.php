<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V3\PasswordResetRequest;
use App\Meedu\ServiceV2\Services\UserServiceInterface;

class PasswordController extends BaseController
{

    /**
     * @api {post} /api/v3/auth/password-reset 重置密码
     * @apiGroup Auth-V3
     * @apiName V3-PasswordReset
     * @apiVersion v3.0.0
     * @apiDescription v5.0新增
     *
     * @apiParam {String} mobile 手机号
     * @apiParam {String} mobile_code 短信验证码
     * @apiParam {String} password 新密码
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     */
    public function reset(PasswordResetRequest $request, UserServiceInterface $userService)
    {
        $this->mobileCodeCheck();

        ['mobile' => $mobile, 'password' => $password] = $request->filldata();

        $user = $userService->findUserByMobile($mobile);
        if (!$user) {
            return $this->error(__('用户不存在'));
        }

        $userService->resetPassword($user['id'], $password);

        return $this->success();
    }

}

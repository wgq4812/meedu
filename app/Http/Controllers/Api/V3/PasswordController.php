<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ApiV3\PasswordResetRequest;
use App\Meedu\ServiceV2\Services\UserServiceInterface;

class PasswordController extends BaseController
{

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

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Requests\Api\V3;

use App\Http\Requests\Api\BaseRequest;

class PasswordResetRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'mobile' => 'required',
            'mobile_code' => 'required',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'mobile.required' => __('请输入手机号'),
            'mobile_code.required' => __('请输入短信验证码'),
            'password.required' => __('请输入密码'),
        ];
    }

    public function filldata()
    {
        return [
            'password' => $this->post('password'),
            'mobile' => $this->post('mobile'),
        ];
    }

}

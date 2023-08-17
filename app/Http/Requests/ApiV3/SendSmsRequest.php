<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Requests\ApiV3;

use App\Http\Requests\ApiV2\BaseRequest;

class SendSmsRequest extends BaseRequest
{

    public function rules()
    {
        $scenes = ['register', 'login', 'password_reset', 'mobile_bind'];

        return [
            'mobile' => 'required',
            'image_captcha' => 'required',
            'image_key' => 'required',
            'scene' => 'required|in:' . implode(',', $scenes),
        ];
    }

    public function messages()
    {
        return [
            'mobile.required' => __('请输入手机号'),
            'image_captcha.required' => __('请输入图形验证码'),
            'image_key.required' => __('参数错误'),
            'scene.required' => __('参数错误'),
            'scene.in' => __('参数错误'),
        ];
    }

    public function filldata()
    {
        return [
            'mobile' => $this->post('mobile'),
            'scene' => $this->post('scene'),
        ];
    }

}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Requests\Api\V2;

use App\Http\Requests\Api\BaseRequest;

class MobileChangeRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'mobile' => 'required',
            'mobile_code' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'mobile.required' => __('请输入手机号'),
            'mobile_code.required' => __('请输入短信验证码'),
        ];
    }

    public function filldata()
    {
        return [
            'mobile' => $this->post('mobile'),
        ];
    }
}

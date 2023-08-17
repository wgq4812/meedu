<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Requests\Api\V2;

use App\Http\Requests\Api\BaseRequest;

class NicknameChangeRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'nick_name' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'nick_name.required' => __('请输入昵称'),
        ];
    }

    public function filldata()
    {
        return [
            'nick_name' => $this->post('nick_name'),
        ];
    }
}

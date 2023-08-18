<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Tests\API\Frontend\V3;

use Illuminate\Support\Str;
use Tests\API\Frontend\Base;
use App\Services\Member\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Meedu\Cache\Impl\SmsCodeCache;

class RegisterControllerTest extends Base
{
    public function test_register_ok()
    {
        $mobile = '18287829922';
        $password = Str::random(6);
        $mobileCode = Str::random(6);

        $smsCodeCache = new SmsCodeCache();
        $smsCodeCache->put($mobile, $mobileCode);

        $response = $this->postJson('/api/v3/auth/register/sms', [
            'mobile' => $mobile,
            'mobile_code' => $mobileCode,
            'password' => $password,
        ]);
        $this->assertResponseSuccess($response);

        $user = User::query()->where('mobile', $mobile)->first();
        $this->assertNotEmpty($user);
        $this->assertTrue(Hash::check($password, $user->password), '注册的密码保存成功');
        $this->assertEquals(0, $user->is_set_nickname, '未设置昵称');
        $this->assertEquals(1, $user->is_password_set, '已设置密码');
    }

    public function test_exists_mobile()
    {
        $mobile = '18287829922';
        $password = Str::random(12);
        $mobileCode = Str::random(6);

        User::factory()->create(['mobile' => $mobile]);

        $smsCodeCache = new SmsCodeCache();
        $smsCodeCache->put($mobile, $mobileCode);

        $response = $this->postJson('/api/v3/auth/register/sms', [
            'mobile' => $mobile,
            'mobile_code' => $mobileCode,
            'password' => $password,
        ]);
        $this->assertResponseError($response, __('手机号已存在'));
    }
}

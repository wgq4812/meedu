<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Tests\API\Frontend\V3;

use Tests\API\Frontend\Base;
use App\Services\Member\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Meedu\Cache\Impl\SmsCodeCache;

class PasswordControllerTest extends Base
{

    public function test_ok()
    {
        $user = User::factory()->create();

        $smsCodeCache = new SmsCodeCache();
        $smsCodeCache->put($user['mobile'], 'code');

        $response = $this->postJson('/api/v3/auth/password-reset', [
            'mobile' => $user['mobile'],
            'mobile_code' => 'code',
            'password' => '123123123'
        ]);

        $this->assertResponseSuccess($response);

        $user->refresh();

        $this->assertTrue(Hash::check('123123123', $user['password']));
    }

}

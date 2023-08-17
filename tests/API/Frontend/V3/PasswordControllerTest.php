<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Tests\API\Frontend\V3;

use Tests\API\Frontend\Base;
use App\Constant\CacheConstant;
use App\Services\Member\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\Base\Interfaces\CacheServiceInterface;

class PasswordControllerTest extends Base
{

    public function test_ok()
    {
        $user = User::factory()->create();

        $cacheService = $this->app->make(CacheServiceInterface::class);
        $cacheService->put(get_cache_key(CacheConstant::MOBILE_CODE['name'], $user['mobile']), 'code', 1);

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

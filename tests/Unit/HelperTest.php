<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Tests\Unit;

use Exception;
use Tests\TestCase;
use App\Meedu\Cache\Impl\SmsCodeCache;
use App\Meedu\Cache\Impl\SmsCodeTryCache;

class HelperTest extends TestCase
{
    public function test_exception_record()
    {
        try {
            throw new Exception('我是异常');
        } catch (Exception $exception) {
            exception_record($exception);
        }
        $this->assertFalse(false);
    }

    public function test_aliyun_sdk_client()
    {
        config([
            'meedu.upload.video.aliyun.access_key_id' => '123',
            'meedu.upload.video.aliyun.access_key_secret' => '456',
        ]);
        aliyun_sdk_client();
        $this->assertTrue(true);
    }

    public function test_array_compress()
    {
        $arr = [
            1 => [
                1 => 1,
                2 => 2,
            ],
        ];
        $arr = array_compress($arr);
        $this->assertEquals(1, $arr['1.1']);
        $this->assertEquals(2, $arr['1.2']);
    }

    public function test_random_number()
    {
        $str = random_number('C', 10);
        $this->assertEquals(10, mb_strlen($str));
    }

    public function test_mobile_code_check()
    {
        $this->assertFalse(mobile_code_check(null, null), '空参数返回false');
        $this->assertFalse(mobile_code_check('13899990002', ''), '空参数返回false');
        $this->assertFalse(mobile_code_check('', '123123'), '空参数返回false');

        $this->assertFalse(mobile_code_check('13899990002', '112233'), 'testing环境固定验证码112233无效');

        $smsCodeCache = new SmsCodeCache();
        $smsCodeTryCache = new SmsCodeTryCache();

        $mobile = '13899990002';

        $smsCodeCache->put($mobile, '123321');
        $this->assertTrue(mobile_code_check($mobile, '123321'));

        // 校验超过之后原先验证码销毁
        $this->assertEquals(0, $smsCodeCache->get($mobile));
        $this->assertEquals(0, $smsCodeTryCache->get($mobile));

        // 校验失败1次之后校验超过
        $smsCodeCache->put($mobile, '123456');
        $this->assertFalse(mobile_code_check($mobile, '123321'));

        $this->assertEquals(1, $smsCodeTryCache->get($mobile));
        $this->assertTrue(mobile_code_check($mobile, '123456'));
        $this->assertEquals(0, $smsCodeCache->get($mobile));
        $this->assertEquals(0, $smsCodeTryCache->get($mobile));

        // 校验失败5次之后校验超过
        $smsCodeCache->put($mobile, '190929');
        for ($i = 1; $i <= 5; $i++) {
            $this->assertFalse(mobile_code_check($mobile, '123321'));
            $this->assertEquals($i, $smsCodeTryCache->get($mobile));
        }
        $this->assertTrue(mobile_code_check($mobile, '190929'));
        $this->assertEquals(0, $smsCodeCache->get($mobile));
        $this->assertEquals(0, $smsCodeTryCache->get($mobile));

        // 校验失败10次之后校验成功
        $smsCodeCache->put($mobile, '189890');
        for ($i = 1; $i <= 10; $i++) {
            $this->assertFalse(mobile_code_check($mobile, '123321'));
            $this->assertEquals($i, $smsCodeTryCache->get($mobile));
        }
        $this->assertTrue(mobile_code_check($mobile, '189890'));
        $this->assertEquals(0, $smsCodeCache->get($mobile));
        $this->assertEquals(0, $smsCodeTryCache->get($mobile));

        // 校验失败11次之后原先验证码失效
        $smsCodeCache->put($mobile, '289800');
        for ($i = 1; $i <= 11; $i++) {
            $this->assertFalse(mobile_code_check($mobile, '123321'));
            // 第11次错误校验导致数据被清空
            if ($i === 11) {
                $this->assertEquals(0, $smsCodeTryCache->get($mobile));
            } else {
                $this->assertEquals($i, $smsCodeTryCache->get($mobile));
            }
        }
        // 超过11次就算第11次给出了正确的验证码也是失败的
        // 因为正确的验证码已经被清空了
        $this->assertFalse(mobile_code_check($mobile, '289800'));
        $this->assertEquals(0, $smsCodeCache->get($mobile));
        $this->assertEquals(1, $smsCodeTryCache->get($mobile));
    }

    public function test_get_array_ids()
    {
        $arr = [
            [
                'id' => 1,
                'name' => 'meedu',
            ],
            [
                'id' => 2,
                'name' => 'meedu2',
            ],
        ];

        $this->assertEquals([1, 2], get_array_ids($arr, 'id'));
        $this->assertEquals(['meedu', 'meedu2'], get_array_ids($arr, 'name'));
    }

    public function test_get_platform()
    {
        $platform = get_platform();
        $this->assertEquals(\App\Constant\FrontendConstant::LOGIN_PLATFORM_APP, $platform);
    }

    public function test_url_append_query()
    {
        $url = 'https://meedu.vip';
        $url1 = 'https://meedu.vip?name=meedu';
        $data = [
            'params1' => 1,
            'params2' => 2,
        ];

        $this->assertEquals('https://meedu.vip?params1=1&params2=2', url_append_query($url, $data));
        $this->assertEquals('https://meedu.vip?name=meedu&params1=1&params2=2', url_append_query($url1, $data));
    }

    public function test_wechat_qrcode_image()
    {
        config([
            'meedu.mp_wechat.app_id' => env('WECHAT_MP_APP_ID', ''),
            'meedu.mp_wechat.app_secret' => env('WECHAT_MP_APP_SECRET', ''),
            'meedu.mp_wechat.token' => env('WECHAT_MP_TOKEN', ''),
        ]);

        $code = 'hello,meedu';
        wechat_qrcode_image($code);
        $this->assertTrue(true);
    }

    public function test_id_mask()
    {
        $this->assertEquals('', id_mask(''));

        // 15位
        $this->assertEquals('110110****123', id_mask('110110890212123'));

        // 18位
        $this->assertEquals('110110****1234', id_mask('110110199502121234'));
    }

    public function test_name_mask()
    {
        $this->assertEquals('', name_mask(''));

        // 15位
        $this->assertEquals('张*', name_mask('张三'));
        $this->assertEquals('李*', name_mask('李四'));

        // 18位
        $this->assertEquals('马*克', name_mask('马斯克'));
    }
}

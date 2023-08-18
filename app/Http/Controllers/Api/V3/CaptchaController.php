<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use App\Meedu\Cache\Impl\SmsCodeCache;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V3\SendSmsRequest;
use App\Meedu\ServiceV2\Services\SmsServiceInterface;
use App\Meedu\ServiceV2\Services\OtherServiceInterface;

class CaptchaController extends BaseController
{
    /**
     * @api {get} /api/v3/captcha/image 图形验证码
     * @apiGroup 其它
     * @apiName V3-CaptchaImage
     * @apiVersion v3.0.0
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.key 随机键值
     * @apiSuccess {String} data.img 图片base64码
     */
    public function image()
    {
        $captcha = app()->make('captcha');
        $data = $captcha->create('default', true);

        return $this->data($data);
    }

    /**
     * @api {post} /api/v3/captcha/sms 发送短信
     * @apiGroup 其它
     * @apiName V3-CaptchaSMS
     * @apiVersion v3.0.0
     *
     * @apiParam {String} mobile 手机号
     * @apiParam {String} image_captcha 图形验证码
     * @apiParam {String} image_key 图形验证码随机值
     * @apiParam {String=login,register,password_reset,mobile_bind} scene scene
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     */
    public function sms(SendSmsRequest $request, SmsServiceInterface $service, OtherServiceInterface $otherService, SmsCodeCache $cache)
    {
        if (is_dev()) {
            return $this->success();
        }

        if (!captcha_image_check()) {
            return $this->error(__('图形验证码错误'));
        }

        ['mobile' => $mobile, 'scene' => $scene] = $request->filldata();

        if (!$cache->beyondLimit($mobile)) {
            return $this->error(__('请 :seconds 秒后再试', ['seconds' => $cache->ttl($mobile)]));
        }

        $code = $cache->get($mobile);
        if (!$code) {
            $code = str_pad(random_int(0, 999999), 6, 0, STR_PAD_LEFT);
        }

        $service->sendCode($mobile, $code, $scene);
        $otherService->storeSmsCodeSendRecord($mobile, $code, $scene);

        $cache->put($mobile, $code);

        return $this->success();
    }

}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use App\Bus\AuthBus;
use App\Bus\WechatScanBus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Constant\CacheConstant;
use App\Constant\FrontendConstant;
use App\Exceptions\ServiceException;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V3\RegisterSmsRequest;
use App\Meedu\ServiceV2\Services\UserServiceInterface;

class RegisterController extends BaseController
{

    /**
     * @api {POST} /api/v3/auth/register/sms 手机密码注册
     * @apiGroup Auth-V3
     * @apiName  V3-RegisterSms
     * @apiVersion v3.0.0
     * @apiDescription v5.0新增
     *
     * @apiParam {String} mobile 手机号
     * @apiParam {String} mobile_code 短信验证码
     * @apiParam {String} password 密码
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.token 用户token
     */
    public function sms(RegisterSmsRequest $request, UserServiceInterface $userService)
    {
        $this->mobileCodeCheck();

        ['mobile' => $mobile, 'password' => $password] = $request->filldata();

        if ($userService->findUserByMobile($mobile)) {
            return $this->error(__('手机号已存在'));
        }

        $userService->createWithMobile($mobile, $password);

        return $this->success();
    }

    /**
     * @api {POST} /api/v3/auth/register/socialite 社交账号注册+手机号绑定
     * @apiGroup Auth-V3
     * @apiName  V3-RegisterSocialite
     * @apiVersion v3.0.0
     * @apiDescription v5.0新增
     *
     * @apiParam {String} code 社交的登录返回的code
     * @apiParam {String} mobile 手机号
     * @apiParam {String} mobile_code 短信验证码
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.token 用户token
     */
    public function socialite(Request $request, AuthBus $authBus, UserServiceInterface $userService)
    {
        $mobile = $request->input('mobile');
        $code = $request->input('code');
        if (!$mobile || !$code) {
            return $this->error(__('参数错误'));
        }

        $this->mobileCodeCheck();

        try {
            $cacheKey = get_cache_key(CacheConstant::USER_SOCIALITE_LOGIN['name'], $code);
            $value = Cache::get($cacheKey);
            if (!$value) {
                throw new ServiceException(__('已过期'));
            }

            $value = unserialize($value);
            $type = $value['type'] ?? null;
            $app = $value['app'] ?? null;
            $data = $value['data'] ?? [];

            if ($type !== 'socialite' || !$app || !isset($data['openid'])) {
                throw new ServiceException(__('参数错误'));
            }

            $openid = $data['openid'];
            $unionId = $data['unionid'] ?? '';
            // 昵称-防止重复
            $nickname = $data['nickname'];
            $nickname && $nickname .= '_' . Str::random(4);
            // 头像
            $avatar = $data['avatar'];

            $userId = $authBus->registerWithSocialite($mobile, $app, $openid, $unionId, $nickname, $avatar, $data);

            // 注册默认锁定判断
            $user = $userService->findUserById($userId);
            if ($user['is_lock'] === 1) {
                throw new ServiceException(__('用户已锁定无法登录'));
            }

            Cache::forget($cacheKey);

            $token = $authBus->tokenLogin($userId, get_platform());

            return $this->data(['token' => $token]);
        } catch (ServiceException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @api {POST} /api/v3/auth/register/wechat-scan 微信扫码登录+手机号绑定
     * @apiGroup Auth-V3
     * @apiName  V3-RegisterWechatScan
     * @apiVersion v3.0.0
     * @apiDescription v5.0新增
     *
     * @apiParam {String} code 微信扫码的code
     * @apiParam {String} mobile 手机号
     * @apiParam {String} mobile_code 短信验证码
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.token 用户token
     */
    public function wechatScan(Request $request, AuthBus $authBus, WechatScanBus $wechatScanBus, UserServiceInterface $userService)
    {
        $mobile = $request->input('mobile');
        $code = $request->input('code');
        if (!$mobile || !$code) {
            return $this->error(__('参数错误'));
        }

        $this->mobileCodeCheck();

        $userData = $wechatScanBus->getLoginUser($code);
        if (!$userData) {
            return $this->error(__('已过期'));
        }

        $userId = $authBus->registerWithSocialite(
            $mobile,
            FrontendConstant::WECHAT_LOGIN_SIGN,
            $userData['openid'],
            $userData['unionid'] ?? '',
            '',
            '',
            $userData
        );

        // 注册默认锁定判断
        $user = $userService->findUserById($userId);
        if ($user['is_lock'] === 1) {
            throw new ServiceException(__('用户已锁定无法登录'));
        }

        // 删除缓存
        $wechatScanBus->delLoginUser($code);

        $token = $authBus->tokenLogin($userId, FrontendConstant::LOGIN_PLATFORM_PC);

        return $this->data(['token' => $token]);
    }

}

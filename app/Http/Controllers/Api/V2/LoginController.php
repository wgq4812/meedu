<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V2;

use App\Bus\AuthBus;
use App\Constant\FrontendConstant;
use App\Events\UserLogoutEvent;
use App\Exceptions\ServiceException;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ApiV2\MobileLoginRequest;
use App\Http\Requests\ApiV2\PasswordLoginRequest;
use App\Services\Base\Interfaces\CacheServiceInterface;
use App\Services\Base\Interfaces\ConfigServiceInterface;
use App\Services\Base\Services\CacheService;
use App\Services\Base\Services\ConfigService;
use App\Services\Member\Interfaces\SocialiteServiceInterface;
use App\Services\Member\Interfaces\UserServiceInterface;
use App\Services\Member\Services\SocialiteService;
use App\Services\Member\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * @var SocialiteService
     */
    protected $socialiteService;

    /**
     * LoginController constructor.
     * @param UserServiceInterface $userService
     * @param ConfigServiceInterface $configService
     * @param CacheServiceInterface $cacheService
     * @param SocialiteServiceInterface $socialiteService
     */
    public function __construct(
        UserServiceInterface      $userService,
        ConfigServiceInterface    $configService,
        CacheServiceInterface     $cacheService,
        SocialiteServiceInterface $socialiteService
    ) {
        $this->userService = $userService;
        $this->configService = $configService;
        $this->cacheService = $cacheService;
        $this->socialiteService = $socialiteService;
    }

    /**
     * @api {post} /api/v2/login/password 密码登录
     * @apiGroup Auth
     * @apiName LoginPassword
     * @apiVersion v2.0.0
     *
     * @apiParam {String} mobile 手机号
     * @apiParam {String} password 密码
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.token token
     */
    public function passwordLogin(PasswordLoginRequest $request)
    {
        [
            'mobile' => $mobile,
            'password' => $password,
        ] = $request->filldata();
        $user = $this->userService->passwordLogin($mobile, $password);
        if (!$user) {
            return $this->error(__('手机号或密码错误'));
        }

        try {
            $token = $this->token($user);

            return $this->data(compact('token'));
        } catch (ServiceException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @api {post} /api/v2/login/mobile 短信登录
     * @apiGroup Auth
     * @apiName LoginMobile
     * @apiVersion v2.0.0
     *
     * @apiParam {String} mobile 手机号
     * @apiParam {String} mobile_code 短信验证码
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object} data 数据
     * @apiSuccess {String} data.token token
     */
    public function mobileLogin(MobileLoginRequest $request)
    {
        $this->mobileCodeCheck();

        ['mobile' => $mobile] = $request->filldata();

        $user = $this->userService->findMobile($mobile);
        if (!$user) {
            // 直接注册
            $user = $this->userService->createWithMobile($mobile, '', '');
        }

        try {
            $token = $this->token($user);

            return $this->data(compact('token'));
        } catch (ServiceException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @param $user
     * @return mixed
     * @throws ServiceException
     */
    protected function token($user)
    {
        if ((int)$user['is_lock'] === 1) {
            throw new ServiceException(__('账号已被锁定'));
        }

        /**
         * @var AuthBus $authBus
         */
        $authBus = app()->make(AuthBus::class);

        return $authBus->tokenLogin($user['id'], get_platform());
    }

    /**
     * @api {post} /api/v2/logout 安全退出
     * @apiGroup Auth
     * @apiName LoginLogout
     * @apiVersion v2.0.0
     *
     * @apiSuccess {Number} code 0成功,非0失败
     */
    public function logout(Request $request)
    {
        $userId = Auth::guard(FrontendConstant::API_GUARD)->id();
        $token = explode(' ', $request->header('Authorization'))[1];
        event(new UserLogoutEvent($userId, $token));

        Auth::guard(FrontendConstant::API_GUARD)->logout();

        return $this->success();
    }
}

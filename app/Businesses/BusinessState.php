<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Businesses;

use Carbon\Carbon;
use App\Constant\FrontendConstant;
use App\Exceptions\ServiceException;
use App\Services\Course\Models\Course;
use App\Services\Base\Services\ConfigService;
use App\Services\Member\Services\UserService;
use App\Services\Course\Services\CourseService;
use App\Services\Member\Services\SocialiteService;
use App\Services\Base\Interfaces\ConfigServiceInterface;
use App\Services\Member\Interfaces\UserServiceInterface;
use App\Services\Course\Interfaces\CourseServiceInterface;
use App\Services\Member\Interfaces\SocialiteServiceInterface;

class BusinessState
{

    /**
     * @param array $user
     * @param array $course
     * @param array $video
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function canSeeVideo(array $user, array $course, array $video): bool
    {
        /**
         * @var CourseService $courseService
         */
        $courseService = app()->make(CourseServiceInterface::class);
        $course = $courseService->find($course['id']);
        // 录播课设置免费的话可以直接看该录播课下所有视频
        if ((int)$course['is_free'] === 1) {
            return true;
        }
        /**
         * @var UserService $userService
         */
        $userService = app()->make(UserServiceInterface::class);
        // 如果用户买了课程可以直接观看
        if ($userService->hasCourse($user['id'], $course['id'])) {
            return true;
        }
        // 如果用户买了会员可以直接观看
        if ($this->isRole($user)) {
            return true;
        }
        // 如果用户买了当前视频可以直接观看
        if ($userService->hasVideo($user['id'], $video['id'])) {
            return true;
        }
        return false;
    }

    /**
     * 订单是否支付.
     *
     * @param array $order
     *
     * @return bool
     */
    public function orderIsPaid(array $order): bool
    {
        return $order['status'] === FrontendConstant::ORDER_PAID;
    }

    /**
     * 是否需要绑定手机号
     *
     * @param array $user
     * @return bool
     */
    public function isNeedBindMobile(array $user): bool
    {
        return substr($user['mobile'], 0, 1) != 1 || mb_strlen($user['mobile']) !== 11;
    }

    /**
     * @param array $user
     * @return bool
     */
    public function isRole(array $user): bool
    {
        if (!$user['role_id'] || !$user['role_expired_at']) {
            return false;
        }
        if (Carbon::now()->gt($user['role_expired_at'])) {
            return false;
        }
        return true;
    }

    /**
     * 是否购买了课程
     *
     * @param int $userId
     * @param int $courseId
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function isBuyCourse(int $userId, int $courseId): bool
    {
        /**
         * @var CourseService $courseService
         */
        $courseService = app()->make(CourseServiceInterface::class);
        $course = $courseService->find($courseId);
        if ($course['is_free'] === Course::IS_FREE_YES) {
            return true;
        }
        /**
         * @var $userService UserService
         */
        $userService = app()->make(UserServiceInterface::class);
        $user = $userService->find($userId, ['role']);
        if ($this->isRole($user)) {
            return true;
        }
        if ($userService->hasCourse($user['id'], $courseId)) {
            return true;
        }
        return false;
    }

    /**
     * 课程是否可以评论
     *
     * @param array $user
     * @param array $course
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function courseCanComment(array $user, array $course): bool
    {
        return $this->isBuyCourse($user['id'], $course['id']);
    }

    /**
     * 课时是否可以评论
     *
     * @param array $user
     * @param array $video
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function videoCanComment(array $user, array $video): bool
    {
        /**
         * @var CourseService $courseService
         */
        $courseService = app()->make(CourseServiceInterface::class);
        $course = $courseService->find($video['course_id']);
        return $this->canSeeVideo($user, $course, $video);
    }

    /**
     * 是否开启了微信公众号授权登录
     *
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function isEnabledMpOAuthLogin(): bool
    {
        /**
         * @var ConfigService $configService
         */
        $configService = app()->make(ConfigServiceInterface::class);
        $mpWechatConfig = $configService->getMpWechatConfig();
        $enabledOAuthLogin = (int)($mpWechatConfig['enabled_oauth_login'] ?? 0);
        return $enabledOAuthLogin === 1;
    }

    /**
     * 是否开启了微信公众号扫码登录
     *
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function enabledMpScanLogin(): bool
    {
        /**
         * @var ConfigService $configService
         */
        $configService = app()->make(ConfigServiceInterface::class);

        $mpWechatConfig = $configService->getMpWechatConfig();

        $enabledOAuthLogin = (int)($mpWechatConfig['enabled_scan_login'] ?? 0);

        return $enabledOAuthLogin === 1;
    }

    /**
     * 用户社交账号绑定检查
     *
     * @param int $userId
     * @param string $app
     * @param string $appId
     * @return void
     * @throws ServiceException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function socialiteBindCheck(int $userId, string $app, string $appId): void
    {
        /**
         * @var SocialiteService $socialiteService
         */
        $socialiteService = app()->make(SocialiteServiceInterface::class);

        $hasBindSocialites = $socialiteService->userSocialites($userId);
        if (in_array($app, array_column($hasBindSocialites, 'app'))) {
            throw new ServiceException(__('您已经绑定了该渠道的账号'));
        }

        // 读取当前社交账号绑定的用户id
        if ($socialiteService->getBindUserId($app, $appId)) {
            throw new ServiceException(__('当前渠道账号已绑定了其它账号'));
        }
    }
}

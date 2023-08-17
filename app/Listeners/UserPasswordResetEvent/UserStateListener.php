<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Listeners\UserPasswordResetEvent;

use App\Events\UserPasswordResetEvent;
use App\Meedu\ServiceV2\Services\UserServiceInterface;

class UserStateListener
{

    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }


    public function handle(UserPasswordResetEvent $event)
    {
        $user = $this->userService->findUserById($event->userId);
        if ($user['is_password_set'] === 0) {
            $this->userService->passwordSetCompleted($user['id']);
        }
    }
}

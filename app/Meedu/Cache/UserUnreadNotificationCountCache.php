<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Cache;

use Illuminate\Support\Facades\Cache;
use App\Meedu\ServiceV2\Services\UserServiceInterface;

class UserUnreadNotificationCountCache
{
    public const KEY = 'u-u-n-c:%d';
    public const EXPIRE = 30;

    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function get(int $userId)
    {
        $key = $this->key($userId);
        $data = Cache::get($key);
        if (!$data) {
            $data = $this->userService->unreadNotificationCount($userId);
            Cache::put($key, $data, self::EXPIRE);
        }
        return $data;
    }

    public function forgot(int $userId)
    {
        Cache::forget($this->key($userId));
    }

    private function key(int $userId): string
    {
        return sprintf(self::KEY, $userId);
    }
}

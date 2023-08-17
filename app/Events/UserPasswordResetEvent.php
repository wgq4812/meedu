<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UserPasswordResetEvent
{
    use Dispatchable, SerializesModels;

    public $userId;

    public function __construct(int $id)
    {
        $this->userId = $id;
    }
}

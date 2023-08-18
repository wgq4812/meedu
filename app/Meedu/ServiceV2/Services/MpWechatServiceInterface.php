<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface MpWechatServiceInterface
{

    public function textMessageReplyFind(string $text): string;

    public function eventMessageReplyFind(string $event, $eventKey = ''): string;

}

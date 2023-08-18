<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Meedu\ServiceV2\Dao\MpWechatDaoInterface;
use App\Meedu\ServiceV2\Models\MpWechatMessageReply;

class MpWechatService implements MpWechatServiceInterface
{

    private $dao;

    public function __construct(MpWechatDaoInterface $dao)
    {
        $this->dao = $dao;
    }

    public function textMessageReplyFind(string $text): string
    {
        $messages = $this->dao->get(['type' => MpWechatMessageReply::TYPE_TEXT], ['id', 'rule']);
        if (!$messages) {
            return '';
        }

        $id = 0;
        foreach ($messages as $message) {
            $rule = $message['rule'] ?? '';
            if (!$rule) {
                continue;
            }
            if (preg_match('#' . $rule . '#', $text)) {
                $id = $message['id'];
                break;
            }
        }

        $message = MpWechatMessageReply::query()->where('id', $id)->first();
        return $message['reply_content'] ?? '';
    }

    public function eventMessageReplyFind(string $event, $eventKey = ''): string
    {
        $messages = $this->dao->get([
            'type' => MpWechatMessageReply::TYPE_EVENT,
            'event_type' => $event,
        ], ['*']);

        if (!$messages) {
            return '';
        }

        $content = '';

        if ($eventKey) {
            foreach ($messages as $message) {
                if (!$message['event_key']) {
                    continue;
                }

                if (preg_match('#' . $message['event_key'] . '#us', $eventKey)) {
                    $content = $message['reply_content'];
                    break;
                }
            }
        } else {
            foreach ($messages as $message) {
                if (!$message['event_key']) {
                    $content = $message['reply_content'];
                    break;
                }
            }
        }

        return $content;
    }


}

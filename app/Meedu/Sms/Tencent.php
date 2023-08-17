<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Sms;

use TencentCloud\Common\Credential;
use App\Exceptions\ServiceException;
use TencentCloud\Sms\V20210111\SmsClient;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Profile\ClientProfile;

use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class Tencent implements SmsInterface
{
    public function sendCode(string $mobile, string $code, string $scene): void
    {
        /**
         * @var ConfigServiceInterface $configService
         */
        $configService = app()->make(ConfigServiceInterface::class);
        $config = $configService->getTencentSmsConfig();

        if (!$config['region'] || !$config['sdk_app_id'] || !$config['secret_id'] || !$config['secret_key'] || !$config['sign_name']) {
            throw new ServiceException(__('腾讯云短信服务为配置'));
        }

        $templateId = $config['template'][$scene] ?? '';
        if (!$templateId) {
            throw new ServiceException(__('短信模板不存在'));
        }

        $cred = new Credential($config['secret_id'], $config['secret_key']);
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint('sms.tencentcloudapi.com');

        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new SmsClient($cred, $config['region'], $clientProfile);

        $req = new SendSmsRequest();

        $params = [
            'PhoneNumberSet' => [$mobile],
            'SmsSdkAppId' => $config['sdk_app_id'],
            'SignName' => $config['sign_name'],
            'TemplateId' => $templateId,
            'TemplateParamSet' => [$code],
        ];
        $req->fromJsonString(json_encode($params));

        $client->SendSms($req);
    }
}

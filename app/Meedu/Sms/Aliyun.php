<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Sms;

use App\Exceptions\ServiceException;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class Aliyun implements SmsInterface
{

    public function sendCode(string $mobile, string $code, string $scene): void
    {
        /**
         * @var ConfigServiceInterface $configService
         */
        $configService = app()->make(ConfigServiceInterface::class);
        $config = $configService->getAliSmsConfig();

        if (!$config['access_key_id'] || !$config['access_key_secret'] || !$config['sign_name']) {
            throw new ServiceException(__('阿里云短信服务未配置'));
        }

        $templateId = $config['template'][$scene] ?? '';
        if (!$templateId) {
            throw new ServiceException(__('短信模板不存在'));
        }

        \AlibabaCloud\Client\AlibabaCloud::accessKeyClient($config['access_key_id'], $config['access_key_secret'])
            ->regionId('cn-hangzhou')
            ->timeout(5)
            ->asDefaultClient();

        $result = \AlibabaCloud\Client\AlibabaCloud::rpc()
            ->product('Dysmsapi')
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->host('dysmsapi.aliyuncs.com')
            ->options([
                'query' => [
                    'PhoneNumbers' => $mobile,
                    'SignName' => $config['sign_name'],
                    'TemplateCode' => $templateId,
                    'TemplateParam' => json_encode([
                        'code' => $code,
                    ]),
                ],
            ])
            ->request();

        $responseCode = $result['Code'];
        $responseMessage = $result['Message'];
        if (!($responseCode === 'OK' && $responseMessage === 'OK')) {
            throw new ServiceException($responseMessage);
        }
    }
}

<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

interface ConfigServiceInterface
{
    /**
     * 获取超级管理员角色的特征值
     * @return string
     */
    public function getSuperAdministratorSlug(): string;

    /**
     * 获取启用的社交登录
     * @return array
     */
    public function getEnabledSocialiteApps(): array;

    /**
     * 获取登录限制规则
     * @return int
     */
    public function getLoginLimitRule(): int;

    /**
     * 是否关闭不推荐的API访问的
     * @return bool
     */
    public function isCloseDeprecatedApi(): bool;

    public function getMpWechatScanLoginAlert(): string;

    public function enabledFaceVerify(): bool;

    public function getVideoDefaultService(): string;

    public function getApiUrl(): string;

    public function getPCUrl(): string;

    public function getH5Url(): string;

    public function getGoMeEduUrl(): string;

    public function getLogo(): string;

    public function getAliCallbackKey(): string;

    public function getTencentFaceConfig(): array;

    public function getTencentVodConfig(): array;

    public function getTencentVodCallbackKey(): string;

    public function getTencentVodPlayKey(): string;

    public function getAliVodConfig(): array;

    public function isEnabledRedisCache(): bool;

    public function isEnabledRedisQueue(): bool;

    public function isEnvTest(): bool;

    public function getAppName(): string;

    public function getICP(): string;

    public function getICPLink(): string;

    public function getICP2(): string;

    public function getICP2Link(): string;

    public function getPlayerCover(): string;

    public function getPlayerBulletSecret(): array;

    public function enabledMustBindMobile(): int;

    public function enabledQQLogin(): int;

    public function enabledWechatScanLogin(): int;

    public function enabledWechatOAUTHLogin(): int;

    public function getCredit1Register(): int;

    public function getCredit1WatchedCourse(): int;

    public function getCredit1WatchedVideo(): int;

    public function getCredit1CreatedPaidOrder(): string;

    public function paymentsStatus(): array;

    public function payments(): array;

    public function enabledPayments(): array;

    public function getHandPayDesc(): string;

    public function getAlipayConfig(): array;

    public function getWechatPayConfig(): array;

    public function getOrderHandler(): array;

    public function getMemberProtocol(): string;

    public function getMemberPrivateProtocol(): string;

    public function getAboutUs(): string;

    public function getEnabledCreateNewAccountOnSmsLogin(): bool;

    public function getMemberDefaultAvatar(): string;

    public function getMemberIsLock(): int;

    public function getMemberIsActive(): int;
}

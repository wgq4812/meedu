<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Traits\ResponseTrait;

/**
 * 影响到系统正常运行的异常
 * 该异常会记录到log中
 * 响应代码为500
 */
class SystemException extends \Exception
{
    use ResponseTrait;

    /**
     * @return JsonResponse
     *
     * @codeCoverageIgnore
     */
    public function render(): JsonResponse
    {
        return $this->error(__('错误'));
    }
}

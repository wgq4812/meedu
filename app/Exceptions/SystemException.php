<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Exceptions;

use App\Http\Controllers\Api\V2\Traits\ResponseTrait;

/**
 * 影响到系统正常运行的异常
 * 该异常会记录到log中
 * 响应代码为500
 */
class SystemException extends \Exception
{
    use ResponseTrait;

    /**
     * @return \Illuminate\Http\JsonResponse|void
     *
     * @codeCoverageIgnore
     */
    public function render()
    {
        return $this->error(__('错误'));
    }
}

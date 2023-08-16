<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Exceptions;

use App\Http\Controllers\Api\Traits\ResponseTrait;

/**
 * 业务异常
 * 抛出该异常系统将不会记录日志当中
 * 且该异常将会返回友好的错误信息
 * 适用于：常见的业务错误、表单校验、数据校验等
 */
class ServiceException extends \Exception
{
    use ResponseTrait;

    public function render()
    {
        if (!is_backend_api()) {
            return $this->error($this->getMessage());
        }
        return response()->json(['status' => 1, 'message' => $this->getMessage()]);
    }
}

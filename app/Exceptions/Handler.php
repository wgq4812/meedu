<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Exceptions;

use Illuminate\Support\Str;
use App\Constant\ApiV2Constant;
use App\Constant\BackendApiConstant;
use Illuminate\Auth\AuthenticationException;
use App\Http\Controllers\Api\Traits\ResponseTrait;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use ResponseTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        ServiceException::class,
        BackendValidateException::class,
        SucAndReturnException::class,
        SystemBaseConfigErrorException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function render($request, \Throwable $e)
    {
        // 如果Exception实现了render方法(也就是自定处理异常的响应)
        if (method_exists($e, 'render')) {
            return parent::render($request, $e);
        }

        // ################################
        // 对未实现render方法的异常类进行拦截处理
        // ################################

        // 后台的异常错误
        if (is_backend_api()) {
            $code = BackendApiConstant::ERROR_CODE;//默认异常代码
            if ($e instanceof AuthenticationException) {//未登录异常
                $code = BackendApiConstant::NO_AUTH_CODE;
            } elseif ($e instanceof ThrottleRequestsException) {//限流异常
                $code = 429;
            }

            return response()->json([
                'status' => $code,
                'message' => $e->getMessage(),
            ]);
        }

        if (Str::contains($request->getUri(), '/api/v2')) {
            $code = ApiV2Constant::ERROR_CODE;//默认异常代码
            if ($e instanceof AuthenticationException) {//未登录401
                $code = ApiV2Constant::ERROR_NO_AUTH_CODE;
            } elseif ($e instanceof ThrottleRequestsException) {//触发限流429
                $code = 429;
            }
            return $this->error(__('错误'), $code);
        }

        return parent::render($request, $e);
    }
}

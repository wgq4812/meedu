<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Exceptions;

use App\Http\Controllers\Api\V2\Traits\ResponseTrait;

/**
 * 系统成功运行并截断异常
 * 使用场景：如果你想在 Hook 中截断系统运行也就是直接返回结果而不是继续运行的话
 *         那么可以抛出此异常
 */
class SucAndReturnException extends \Exception
{
    use ResponseTrait;

    private $with;

    public function __construct($with, $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->with = $with;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @codeCoverageIgnore
     */
    public function render()
    {
        return $this->data($this->with, 0, $this->message);
    }
}

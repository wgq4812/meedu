<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Backend\Traits\ResponseTrait;

class SystemBaseConfigErrorException extends \Exception
{
    use ResponseTrait;

    /**
     * @return JsonResponse
     *
     * @codeCoverageIgnore
     */
    public function render(): JsonResponse
    {
        if (is_backend_api()) {
            return $this->error($this->getMessage(), 1001);
        }
        return response()->json(['code' => 1, 'message' => $this->getMessage()]);
    }
}

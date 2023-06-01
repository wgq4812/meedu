<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Exceptions;

use App\Http\Controllers\Backend\Traits\ResponseTrait;

class BackendValidateException extends \Exception
{
    use ResponseTrait;

    public function render()
    {
        return $this->error($this->getMessage());
    }
}

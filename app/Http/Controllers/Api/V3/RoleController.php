<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Api\BaseController;
use App\Meedu\ServiceV2\Services\RoleServiceInterface;

class RoleController extends BaseController
{
    /**
     * @api {get} /api/v3/roles 全部VIP会员
     * @apiGroup VIP会员-V3
     * @apiName Roles
     * @apiVersion v3.0.0
     * @apiDescription v5.0 新增
     *
     * @apiSuccess {Number} code 0成功,非0失败
     * @apiSuccess {Object[]} data
     * @apiSuccess {String} data.name VIP名
     */
    public function index(RoleServiceInterface $roleService)
    {
        $data = $roleService->all();
        $data = arr2_clear($data, ['id', 'name', 'charge', 'expire_days', 'description']);
        return $this->data($data);
    }

}

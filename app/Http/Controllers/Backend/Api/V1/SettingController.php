<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Backend\Api\V1;

use App\Meedu\Core\Setting;
use Illuminate\Http\Request;
use App\Models\AdministratorLog;
use App\Events\AppConfigSavedEvent;

class SettingController extends BaseController
{
    public function index(Setting $setting)
    {
        $config = $setting->getCanEditConfig();
        foreach ($config as $key => $val) {
            // 可选值
            if ($val['option_value']) {
                $config[$key]['option_value'] = json_decode($val['option_value'], true);
            }
            // 私密信息
            if ((int)$val['is_private'] === 1 && $config[$key]['value']) {
                $config[$key]['value'] = str_pad('', 12, '*');
            }
        }
        $data = [];
        foreach ($config as $item) {
            if (!isset($data[$item['group']])) {
                $data[$item['group']] = [];
            }
            $item['is_show'] === 1 && $data[$item['group']][] = $item;
        }

        AdministratorLog::storeLog(
            AdministratorLog::MODULE_SYSTEM_CONFIG,
            AdministratorLog::OPT_VIEW,
            []
        );

        return $this->successData($data);
    }

    public function saveHandler(Request $request, Setting $setting)
    {
        $data = $request->input('config');
        $setting->append($data);

        event(new AppConfigSavedEvent());

        AdministratorLog::storeLog(
            AdministratorLog::MODULE_SYSTEM_CONFIG,
            AdministratorLog::OPT_UPDATE,
            []
        );

        return $this->success();
    }
}

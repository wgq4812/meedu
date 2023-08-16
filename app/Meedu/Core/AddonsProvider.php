<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\Core;

use Illuminate\Contracts\Foundation\Application;

class AddonsProvider
{
    /**
     * @param Application $app
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function bootstrap(Application $app)
    {
        $meeduAddons = new Addons();
        $providers = $meeduAddons->getProvidersMap();
        if ($providers) {
            array_map(function ($provider) use ($app) {
                $app->register($provider);
            }, $providers);
        }
    }
}

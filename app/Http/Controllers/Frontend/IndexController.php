<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Meedu\ServiceV2\Services\ConfigServiceInterface;

class IndexController extends Controller
{

    public function index()
    {
        return __('API服务正在运行');
    }

    public function userProtocol(ConfigServiceInterface $configService)
    {
        $protocol = $configService->getMemberProtocol();
        return view('index.user_protocol', compact('protocol'));
    }

    public function userPrivateProtocol(ConfigServiceInterface $configService)
    {
        $protocol = $configService->getMemberPrivateProtocol();
        return view('index.user_private_protocol', compact('protocol'));
    }

    public function aboutus(ConfigServiceInterface $configService)
    {
        $aboutus = $configService->getAboutUs();
        return view('index.aboutus', compact('aboutus'));
    }

    public function faceVerifySuccess()
    {
        return view('index.face_verify_success');
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\Controller\StatisticsControllerService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class StatisticsController extends Controller
{
    public function getStatisticsTemplate(
        UserService $userService,
        StatisticsControllerService $statisticsControllerService
    ) {
        return $statisticsControllerService->getStatisticsTemplate($userService, Auth::id());
    }
}

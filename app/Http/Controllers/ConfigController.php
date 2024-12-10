<?php

namespace App\Http\Controllers;

use App\Services\Controller\ConfigControllerService;
use App\Services\UserService;

class ConfigController extends Controller
{
    public function getConfigTemplate(UserService $userService, ConfigControllerService $configControllerService)
    {
        return $configControllerService->getConfigTemplate($userService);
    }
}

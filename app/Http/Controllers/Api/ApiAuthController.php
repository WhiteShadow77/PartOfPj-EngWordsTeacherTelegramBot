<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiLoginRequest;
use App\Services\Auth\Api\ApiAuthService;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    private ApiAuthService $apiAuthService;

    public function __construct(ApiAuthService $apiAuthService)
    {
        $this->apiAuthService = $apiAuthService;
    }

    public function login(ApiLoginRequest $request)
    {
        return $this->apiAuthService->login($request);
    }

    public function logout(Request $request)
    {
        return $this->apiAuthService->logout($request);
    }
}

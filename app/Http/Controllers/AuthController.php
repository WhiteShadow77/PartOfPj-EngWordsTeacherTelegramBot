<?php

namespace App\Http\Controllers;

use App\Services\Auth\AuthService;
use App\Services\GuestService;
use App\Traits\LoggerTrait;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use LoggerTrait;

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @throws Exception
     */
    public function getLoginPage(Request $request, GuestService $guestService)
    {
        return $this->authService->getLoginPage($request, $guestService);
    }

    public function logout()
    {
        return $this->authService->logout();
    }

    public function getUnknownUserPage(GuestService $guestService)
    {
        return $this->authService->getUnknownUserPage($guestService);
    }
}

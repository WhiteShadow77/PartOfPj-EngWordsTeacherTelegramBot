<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GuestService;
use App\Services\ResponseService;
use App\Traits\LoggerTrait;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    use LoggerTrait;

    private GuestService $guestService;

    public function __construct(GuestService $guestService)
    {
        $this->guestService = $guestService;
    }

    public function setLanguage(Request $request, ResponseService $responseService)
    {
        return $this->guestService->setLanguage($request->language, $responseService);
    }
}

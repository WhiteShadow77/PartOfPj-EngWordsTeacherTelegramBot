<?php

namespace App\Http\Controllers;

use App\Services\GuestService;
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

    public function getMainPage()
    {
        return $this->guestService->getMainPage();
    }
}

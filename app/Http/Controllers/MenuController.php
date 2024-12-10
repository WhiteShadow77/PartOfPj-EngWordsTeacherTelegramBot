<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Services\Controller\MenuControllerService;

class MenuController extends Controller
{
    public function __construct(private MenuControllerService $menuControllerService)
    {
    }

    public function getPage()
    {
        return $this->menuControllerService->getPage();
    }

    public function getStackPage()
    {
        return $this->menuControllerService->getStackPage();
    }
}

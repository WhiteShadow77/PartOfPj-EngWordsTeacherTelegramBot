<?php

namespace App\Http\Controllers;

use App\Services\Log\LogService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function getLogText(LogService $logService, Request $request)
    {
        return $logService->responseWithLogText($request);
    }
}

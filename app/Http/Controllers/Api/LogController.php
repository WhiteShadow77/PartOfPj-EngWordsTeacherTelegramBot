<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Log\LogService;
use App\Services\ResponseService;

class LogController extends Controller
{
    public function twitchIsEnabledWriteLog(ResponseService $responseService, LogService $logService)
    {
        return $logService->twitchIsEnabledWriteLog($responseService);
    }

    public function twitchIsEnabledSendLog(ResponseService $responseService, LogService $logService)
    {
        return $logService->twitchIsEnabledSendLog($responseService);
    }

    public function getLogsConfig(ResponseService $responseService, LogService $logService)
    {
        return $logService->getLogsConfig($responseService);
    }

    public function getLogTextAccessLink(ResponseService $responseService, LogService $logService)
    {
        return $logService->getLogTextAccessLink($responseService);
    }

    public function clearLogFile(ResponseService $responseService, LogService $logService)
    {
        return $logService->clearLogFile($responseService);
    }
}

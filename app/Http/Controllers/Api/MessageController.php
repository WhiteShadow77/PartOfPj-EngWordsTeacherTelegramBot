<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Cache\LanguageCacheService;
use App\Services\History\HistoryRecordService;
use App\Services\History\HistoryMessageService;
use App\Services\TelegramService;
use App\Traits\LoggerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use LoggerTrait;

    public function receive(
        Request $request,
        TelegramService $telegramService,
        HistoryRecordService $historyRecordService,
        HistoryMessageService $historyMessageService,
        LanguageCacheService $languageCacheService
    ): JsonResponse {
        $this->writeInfoLog('Received message', $request->all());
        return $telegramService->receiveMessage(
            $request,
            $historyRecordService,
            $historyMessageService,
            $languageCacheService
        );
    }
}

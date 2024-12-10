<?php

namespace App\Http\Controllers;

use App\Services\Cache\LanguageCacheService;
use App\Services\Controller\HistoryControllerService;
use App\Services\History\HistoryMessageService;
use App\Services\History\HistoryRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\SentWordsKind;
use App\Enums\WordKind;
use App\Enums\WordStatus;
use App\Enums\AnswerKind;
use App\Enums\MessageType;

class HistoryController extends Controller
{
    private HistoryControllerService $historyControllerService;

    public function __construct(HistoryControllerService $historyControllerService)
    {
        $this->historyControllerService = $historyControllerService;
    }

    public function getHistoryTemplate(HistoryRecordService $historyService)
    {
        $userId = Auth::user()->id;
        return $this->historyControllerService->getHistoryTemplate($userId, $historyService);
    }

    public function deleteHistory(
        Request $request,
        HistoryRecordService $historyRecordService,
        HistoryMessageService $historyMessageService,
        LanguageCacheService $languageCacheService
    ) {
        $userId = Auth::user()->id;
        $chatId = Auth::user()->chat_id;

        return $this->historyControllerService->deleteHistory(
            $request->delete_history_period,
            $userId,
            $chatId,
            $historyRecordService,
            $historyMessageService,
            $languageCacheService
        );
    }
}

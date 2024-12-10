<?php

namespace App\Services\Controller;

use App\Services\Cache\LanguageCacheService;
use App\Services\History\HistoryMessageService;
use App\Services\History\HistoryRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\SentWordsKind;
use App\Enums\WordKind;
use App\Enums\WordStatus;
use App\Enums\AnswerKind;
use App\Enums\MessageType;

class HistoryControllerService
{
    public function getHistoryTemplate(int $userId, HistoryRecordService $historyService)
    {
        return view('home.history', [
            'answerKind_right' => (AnswerKind::right)->name,
            'wordStatus_known' => (WordStatus::known)->name,
            'wordKind_englishWord' => (WordKind::english_word)->name,
            'messageType_info' => (MessageType::info)->name,
            'messageType_infoDeletion' => (MessageType::info_deletion)->name,
            'data' => $historyService->getAllRecordsForFrontEnd($userId)
        ]);
    }

    public function deleteHistory(
        int $deleteHistoryPeriod,
        int $userId,
        string $chatId,
        HistoryRecordService $historyRecordService,
        HistoryMessageService $historyMessageService,
        LanguageCacheService $languageCacheService
    ) {
        $historyRecordService->deleteByMonthsPeriod(
            $deleteHistoryPeriod,
            $userId,
            $historyMessageService,
            $languageCacheService,
            $chatId
        );

        return redirect()->route('history');
    }
}

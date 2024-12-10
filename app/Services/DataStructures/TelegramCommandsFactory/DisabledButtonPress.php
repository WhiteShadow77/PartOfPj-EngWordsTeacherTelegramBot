<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\History\HistoryMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\History\HistoryRecordService;
use Illuminate\Support\Facades\App;

class DisabledButtonPress extends TelegramCommandFactory
{
    public function run(
        UserService $userService,
        HistoryRecordService $historyRecordService,
        HistoryMessageService $historyMessageService,
        ?object $messageData,
        AuthRedisService $authRedisService,
        TelegramService $telegramService,
        EnglishWordService $englishWordService,
        string $chatId,
        LanguageCacheService $languageCacheService
    ) {
        $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
        App::setLocale($userLanguage);

        $text = __("This button is no longer active") . '.';
        //'Эта кнопка уже не активна.'
        $telegramService->sendMessage(
            $chatId,
            $text
        );
        return response()->json(['ok' => 'right answer']);
    }
}

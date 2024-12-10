<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\History\HistoryRecordService;
use App\Services\History\HistoryMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Enums\MessageType;
use Illuminate\Support\Facades\App;

class Start extends TelegramCommandFactory
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
        $userModel = $userService->createUser(
            $messageData->from->firstName,
            $messageData->from->lastName,
            $messageData->from->userName,
            $messageData->from->id,
            $messageData->from->languageCode,
            $messageData->chat->id,
            $messageData->chat->type
        );
        $authRedisService->addTelegramUserId($messageData->from->id);
        $telegramService->sendMessage(
            $chatId,
            'Привет, ' . $messageData->from->firstName . ' ' . hex2bin('F09F918B')
        );

        $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
        App::setLocale($userLanguage);

        $historyMessageService->addRecord(
            __("Started using") . hex2bin('F09F988A'),
            $userModel->id,
            MessageType::info
        );

        return response()->json(['ok' => 'Started']);
    }
}

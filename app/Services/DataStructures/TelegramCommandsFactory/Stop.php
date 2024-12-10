<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\History\HistoryMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\History\HistoryRecordService;

class Stop extends TelegramCommandFactory
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
        $telegramService->sendMessage(
            $chatId,
            'Bye, bye, ' . $messageData->from->firstName . ' ' . hex2bin('F09F918B')
        );

        $userService->deleteUser($messageData->from->id);

        $authRedisService->deleteValueByTelegramUserId($messageData->from->id);
        $languageCacheService->freeCache($chatId);

        return response()->json(['ok' => 'Stopped']);
    }
}

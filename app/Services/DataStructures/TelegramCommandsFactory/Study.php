<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Jobs\KnownStudyWordUserJob;
use App\Jobs\StudyWordUserJob;
use App\Jobs\UnknownStudyWordUserJob;
use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\History\HistoryMessageService;
use App\Services\Cache\StudyWordCacheService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\History\HistoryRecordService;
use App\Enums\MessageType;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

class Study extends TelegramCommandFactory
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

        if (is_null($this->arguments)) {
            $telegramService->answerToReply(
                $messageData->chat->id,
                __("Input error. There is no word entered after the command") . '.',
                $messageData->messageId
            );

            $text = __("Example command: /study apple orange grape");
            $buttonsStruct = [
                [[
                    'text' => __("Learn more about the /study command"),
                    'callback_data' => '#help study',
                ]]
            ];
            $telegramService->sendMessageAndButtons(
                $chatId,
                $text,
                $buttonsStruct
            );

            return response()->json(['ok' => 'Unknown command parameter']);
        }

        $userModel = $userService->getUserByTelegramUserId($messageData->from->id);

        if ($userModel) {
            $cacheIdentifier = Str::uuid();
            $studyWordCacheService = new StudyWordCacheService();

            foreach ($this->arguments as $argument) {
                $studyWordId = $userService->addOrCreateStudyWordAndAddToUser($userModel, $argument);
                $studyWordCacheService->setStudyWordIdInCache($cacheIdentifier, $studyWordId);
            }

            $chain = [
                new StudyWordUserJob($userModel, $cacheIdentifier, true),
                new KnownStudyWordUserJob($userModel, $cacheIdentifier, $this->arguments),
                new UnknownStudyWordUserJob($userModel, $cacheIdentifier, $this->arguments)
            ];
            Bus::chain($chain)->onQueue('study_words')->dispatch();
        } else {
            throw new \Exception('User not found by telegram user id');
        }

        $historyMessageService->addRecord(
            __("Entered words to study") . ':',
            $userModel->id,
            MessageType::info,
            $this->arguments
        );

        return response()->json(['ok' => 'Study job is running']);
    }
}

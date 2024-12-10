<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\Helpers\FieldId;
use App\Services\History\HistoryMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\StatisticsService;
use App\Services\History\HistoryRecordService;
use App\Enums\WordKind;
use Illuminate\Support\Facades\App;

class RightAnswer extends TelegramCommandFactory
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
        // $arguments['rightAnswerWord'] =  $arguments[1]

        $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
        App::setLocale($userLanguage);

        if (is_null($this->arguments)) {
            $telegramService->answerToReply(
                $messageData->chat->id,
                __("Response error") . '.',
                $messageData->messageId
            );
            return response()->json(['ok' => 'Response error']);
        }

        $telegramService->sendMessage(
            $chatId,
            __("Congratulations") . '!  ' . hex2bin('F09F8E89') . ' \'' . $this->arguments[1] . '\' ' .
            __("it's the correct answer") . PHP_EOL .
            __("P.S. I will mark this word as learned") . ' ' . hex2bin('F09F92AA')
        );
        $userModel = $userService->getUserByTelegramUserId($messageData->from->id);

        if ($userService->hasSentStudyWord($userModel, str_replace('_', ' ', $this->arguments[1]))) {
            $userService->addKnownWord(
                $userModel,
                $this->arguments[1],
                WordKind::study_word,
                new StatisticsService(),
                $historyRecordService
            );
        } else {
            $userService->addKnownWord(
                $userModel,
                $this->arguments[1],
                WordKind::english_word,
                new StatisticsService(),
                $historyRecordService
            );
        }

        $userService->setQuizAnswerIsReceivedParamReceived($userModel);

        $cacheFieldId = FieldId::makeFromModel($userModel, $this->arguments[3]);
        $buttonsStruct = $telegramService->getButtonsStructFromCacheAndFree($userModel->id, $cacheFieldId);
        $telegramService->disableButtonsCallbackOfMessageSetRightAnswer(
            $userModel->chat_id,
            $this->messageId,
            $buttonsStruct
        );
        return response()->json(['ok' => 'Right answer']);
    }
}

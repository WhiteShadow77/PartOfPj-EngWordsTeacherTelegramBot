<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\Helpers\FieldId;
use App\Services\History\HistoryMessageService;
use App\Services\Cache\QuizCacheService;
use App\Services\StatisticsService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\History\HistoryRecordService;
use App\Enums\WordKind;
use App\Enums\WordStatus;
use App\Enums\AnswerKind;
use Illuminate\Support\Facades\App;

class WrongAnswer extends TelegramCommandFactory
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
        //$arguments['buttonChosenVariant'] = $arguments[2]
        //$arguments[3] is time which is used as id
        //$arguments[4] is right answer word id

        if (is_null($this->arguments)) {
            $telegramService->answerToReply(
                $messageData->chat->id,
                __("Response error") . '.',
                $messageData->messageId
            );
            //'Ошибка ответа.'
            return response()->json(['ok' => 'Response error']);
        }

        $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
        App::setLocale($userLanguage);

        $translateVariants = $englishWordService->translate(str_replace('_', ' ', $this->arguments[2]));
        $translateMessage = '';
        foreach ($translateVariants as $translate) {
            $translateMessage .= '- ';
            $translateMessage .= $translate . PHP_EOL;
        }
        $telegramService->sendMessage(
            $chatId,
            __("This is not the correct answer") . '.' . PHP_EOL . PHP_EOL .
            __("Correct answer is") . ': ' . $this->arguments[1] . '.' . PHP_EOL . PHP_EOL .
            $this->arguments[2] . ' ' . __("is translated as") . ':' . PHP_EOL . $translateMessage . PHP_EOL .
            __("P.S. I'll ask again later") . '.'
        );

        $userModel = $userService->getUserByTelegramUserId($messageData->from->id);

        if ($userService->hasSentStudyWord($userModel, str_replace('_', ' ', $this->arguments[1]))) {
            $historyRecordService->addRecord(
                $this->arguments[2],
                AnswerKind::wrong,
                WordStatus::unknown,
                WordKind::study_word,
                $userModel->id,
                null,
                $this->arguments[1]
            );
        } else {
            $historyRecordService->addRecord(
                $this->arguments[2],
                AnswerKind::wrong,
                WordStatus::unknown,
                WordKind::english_word,
                $userModel->id,
                null,
                $this->arguments[1]
            );
        }

        $statisticsService = new StatisticsService();
        $statisticsService->addRecord($userModel->id, WordStatus::unknown);

        $userService->setQuizAnswerIsReceivedParamReceived($userModel);

        $cacheFieldId = FieldId::makeFromModel($userModel, $this->arguments[3]);

        $buttonsStruct = $telegramService->getButtonsStructFromCacheAndFree($userModel->id, $cacheFieldId);
        $telegramService->disableButtonsCallbackOfMessageSetWrongAnswer(
            $chatId,
            $this->messageId,
            $buttonsStruct,
            $this->arguments[2]
        );

        $quizCacheService = new QuizCacheService();
        $quizCacheService->setWrongAnsweredQuestionRightWordId($cacheFieldId, $this->arguments[4]);

        return response()->json(['ok' => 'Wrong answer']);
    }
}

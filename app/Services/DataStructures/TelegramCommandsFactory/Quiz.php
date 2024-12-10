<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Jobs\QuizUserAnswerWaitableJob;
use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\Helpers\FieldId;
use App\Services\History\HistoryMessageService;
use App\Services\Cache\QuizCacheService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\History\HistoryRecordService;
use App\Enums\WordKind;
use App\Enums\MessageType;
use App\Enums\QuizPower;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class Quiz extends TelegramCommandFactory
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
        //$arguments[2] is user id
        //$arguments[3] is quizes quantity or buttons message id (time as id) if discarded
        //$arguments[4] is buttons message id (time as id) if not discarded

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

        $userModel = User::find($this->arguments[2]);

        if ($this->arguments[1] == 'start') {
            $quizesBatch = [];
            for ($i = 1; $i <= $this->arguments[3]; $i++) {
                $quizesBatch[] = new QuizUserAnswerWaitableJob($userModel, $this->arguments[4]);
            }

            $cacheFieldId = FieldId::makeFromModel($userModel, $this->arguments[4]);

            //$quizesChain[];

//            $quizesChain[] = function () use ($cacheFieldId , $telegramService, $userModel){
//                $quizCacheService = new QuizCacheService();
//                $quizCacheService->freeWrongAnswerWordsIdsMemeory($cacheFieldId);
//                $telegramService->sendMessage($userModel->chat_id, __("Test completed") . '.');
//            };

//            Bus::chain($quizesChain)->onQueue('quiz')->dispatch();

            $batch = Bus::batch($quizesBatch)
                ->onQueue('quiz')
                ->finally(function () use ($cacheFieldId, $telegramService, $userModel) {
                    $quizCacheService = new QuizCacheService();
                    $quizCacheService->freeWrongAnswerWordsIdsMemeory($cacheFieldId);
                    $telegramService->sendMessage($userModel->chat_id, __("Test completed") . '.');
                })
                ->dispatch();
            $buttonsStruct = $telegramService->getButtonsStructFromCacheAndFree($userModel->id, $cacheFieldId);
        }

        if ($this->arguments[1] == 'discard') {
            $text = __("Test canceled") . '.';
            $telegramService->sendMessage($chatId, $text);

            $historyMessageService->addRecord($text, $userModel->id, MessageType::error);
            $cacheFieldId = FieldId::makeFromModel($userModel, $this->arguments[3]);
            $buttonsStruct = $telegramService->getButtonsStructFromCacheAndFree($userModel->id, $cacheFieldId);
        }

        $telegramService->disableAllButtonsCallbackOfMessage(
            $userModel->chat_id,
            $this->messageId,
            $buttonsStruct,
            false
        );

        return response()->json(['ok' => 'Right answer']);
    }
}

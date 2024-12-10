<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\DataStructures\EnglishWordsSchedule\WeekSchedule;
use App\Services\EnglishWordService;
use App\Services\Helpers\FieldId;
use App\Services\History\HistoryMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\StatisticsService;
use App\Services\History\HistoryRecordService;
use App\Enums\WordKind;
use App\Enums\MessageType;
use App\Jobs\TwitchUserJob;
use App\Models\User;
use App\Enums\SendScheduleKind;
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\App;

class Twitch extends TelegramCommandFactory
{
    use LoggerTrait;

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
        //$arguments[3] is buttons struct identifier as time

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
            $text = __("Wait, I'm preparing the words") . hex2bin('F09F988A');
            $telegramService->sendMessage(
                $chatId,
                $text
            );

            $text = __("Preparing words for extraordinary distribution") . '.';
            $historyMessageService->addRecord($text, $userModel->id, MessageType::info);

            $weekScheduleInstance = new WeekSchedule();
            $nextSendingDate = $weekScheduleInstance->getNextScheduleSendDate(
                $userModel->id,
                SendScheduleKind::quiz
            );

            $AfterJobExecutionText = __("Ready. I'll test you later") . ', ' . __('messages.twitch_word_in_before_day') .
                ' ' . __('messages.' . key($nextSendingDate)) .
                ' ' . __('messages.twitch_word_in_before_time') . ' ' . current($nextSendingDate) .
                '. ' . __("Good luck with your studies") . hex2bin('F09F988A');

            TwitchUserJob::dispatch($userModel, $AfterJobExecutionText)->onQueue('twitch');
        }

        if ($this->arguments[1] == 'discard') {
            $weekScheduleInstance = new WeekSchedule();
            $nextSendingDate = $weekScheduleInstance
                ->getNextScheduleSendDate($userModel->id, SendScheduleKind::english_words);

            $hasTag = '#study';
            $text = __("I'm canceling the distribution of words, I'll send it later") . ', ' .
                __("messages.twitch_word_in_before_day") . ' ' .
                __('messages.' . key($nextSendingDate)) . ' ' .
                __("messages.twitch_word_in_before_time") . ' ' .
                current($nextSendingDate) . hex2bin('F09F988A') . ' ' .
                __("You can also enter words to study using the /study command") .
                PHP_EOL . PHP_EOL . '(hash tag #study)';

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

            $text = __("Distribution of words canceled") . '.';
            $historyMessageService->addRecord($text, $userModel->id, MessageType::info);
        }

        $cacheFieldId = FieldId::makeFromModel($userModel, $this->arguments[3]);
        $buttonsStruct = $telegramService->getButtonsStructFromCacheAndFree($userModel->id, $cacheFieldId);
        $telegramService->disableAllButtonsCallbackOfMessage($chatId, $this->messageId, $buttonsStruct, false);

        return response()->json(['ok' => 'Right answer']);
    }
}

<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\Comment\TelegramCommentService;
use App\Services\EnglishWordService;
use App\Services\History\HistoryMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\History\HistoryRecordService;
use App\Enums\WordKind;
use App\Enums\MessageType;
use App\Enums\QuizPower;
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\App;

class Comment extends TelegramCommandFactory
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
        $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
        App::setLocale($userLanguage);

        if (is_null($this->arguments)) {
            $telegramService->answerToReply(
                $messageData->chat->id,
                __("Wrong comment input") . '.',
                $messageData->messageId
            );

            $text = __("Example command: /comment Your comment text - here") . '.';
            $buttonsStruct = [
                [[
                    'text' => __("Learn more about the /comment command"),
                    'callback_data' => '#help comment',
                ]]
            ];
            $telegramService->sendMessageAndButtons(
                $chatId,
                $text,
                $buttonsStruct
            );

            return response()->json(['ok' => 'Response error']);
        }

        $errorMessage = null;

        $text = implode(' ', $this->arguments);

        $telegramCommentService = new TelegramCommentService();
        $isCommentPosted = $telegramCommentService->postComment($chatId, $text, $errorMessage);

        if ($isCommentPosted) {
            $text = __(
                "Comment has been posted. I will pass it on to the development team and they will definitely study it"
            ) . hex2bin('F09F988A');
            $telegramService->sendMessage($chatId, $text);
        } else {
            $telegramService->sendMessage(
                $chatId,
                __("Comment has not been received. Error occurred") . ': ' . __($errorMessage) . '. ' .
                __("Max quantity of comments is") . ': ' . config('comment.max_quantity_limit')
            );
        }

        return response()->json(['ok' => 'Right answer']);
    }
}

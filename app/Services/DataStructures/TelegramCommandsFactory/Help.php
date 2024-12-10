<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\Cache\MenuCacheService;
use App\Services\EnglishWordService;
use App\Services\History\HistoryMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\History\HistoryRecordService;
use Illuminate\Support\Facades\App;

class Help extends TelegramCommandFactory
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
        $menuCacheService = new MenuCacheService($chatId);

        $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
        App::setLocale($userLanguage);

        if (isset($this->arguments[1]) && $this->arguments[1] == 'study') {
            $text = __("The /study command is a string of English words you want to study, separated by spaces") . '.' .
                PHP_EOL . PHP_EOL . __("For example: /study apple orange grape") . PHP_EOL . PHP_EOL .
                __("Using this command, I will send a translation of these words for your study, I will remember them and conduct a test after a while") .
                hex2bin('F09F988A') . PHP_EOL . PHP_EOL;

            if (isset($this->arguments[2]) && $this->arguments[2] == 'without_button') {
                $telegramService->sendMessage(
                    $chatId,
                    $text
                );
            } else {
                $text .= __("You can see the test time and other settings in your personal account") .
                    hex2bin('F09F988A') .
                    PHP_EOL . PHP_EOL . '(hash tag #profile)';

                $buttonsStruct = [
                    [[
                        'text' => __("See personal account"),
                        'callback_data' => '#profile',
                    ]]
                ];

                $telegramService->sendMessageAndButtons(
                    $chatId,
                    $text,
                    $buttonsStruct
                );
            }
        } elseif (isset($this->arguments[1]) && $this->arguments[1] == 'comment') {
            $text = __("The /comment command is the text of your comment, entered separated by a space after /comment. Developers will read it") . '.' .
                PHP_EOL . PHP_EOL . __("For example: /comment Hi, everything is working great") . '.';

            $telegramService->sendMessage(
                $chatId,
                $text
            );
        } elseif (isset($this->arguments[1]) && $this->arguments[1] == 'close') {
            $telegramService->deleteMessage(
                $chatId,
                $menuCacheService->getCallHelpMenuMessageIdAndFreeCache()
            );

            $telegramService->deleteMessage(
                $chatId,
                $menuCacheService->getHelpMenuMessageIdAndCacheFree()
            );
        } else {
            if (!is_null($menuCacheService->getCallHelpMenuMessageId())) {
                $telegramService->deleteMessage(
                    $chatId,
                    $menuCacheService->getCallHelpMenuMessageIdAndFreeCache()
                );
            }

            if (!is_null($menuCacheService->getHelpMenuMessageId())) {
                $telegramService->deleteMessage(
                    $chatId,
                    $menuCacheService->getHelpMenuMessageIdAndCacheFree()
                );
            }

            $messageId = $messageData ?->messageId ?? null;
            $menuCacheService->setCallHelpMenuMessageId($messageId);

            $text =  __("Select the command which description you want to know") .
                '. ' . PHP_EOL . PHP_EOL . '(hash tag #help)';

            $buttonsStruct = [
                [[
                    'text' => '/study',
                    'callback_data' => '#help study without_button',
                ]],
                [[
                    'text' => '/comment',
                    'callback_data' => '#help comment',
                ]],
                [[
                    'text' => __("Close"),
                    'callback_data' => '#help close',
                ]]
            ];

            /** @var $sentMessageId */

            $telegramService->sendMessageAndButtons(
                $chatId,
                $text,
                $buttonsStruct,
                $sentMessageId
            );

            $menuCacheService->setHelpMenuMessageId($sentMessageId);
        }
        return response()->json(['ok' => 'Help']);
    }
}

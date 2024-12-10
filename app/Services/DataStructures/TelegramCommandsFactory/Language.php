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
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\App;
use App\Enums\MessageType;

class Language extends TelegramCommandFactory
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

        $menuCacheService = new MenuCacheService($chatId);
        $messageId = $messageData ?->messageId ?? null;

        if (!is_null($messageId)) {
            $menuCacheService->setCallLanguageMenuMessageId($messageId);
        }

        $languageOrCloseArg = isset($this->arguments[1]) ? $this->arguments[1] : null;

        if (!is_null($languageOrCloseArg)) {
            if (isset(config('language.available_kinds')[$languageOrCloseArg])) {
                $userModel = $userService->getUserModelByChatId($chatId);

                $languageCacheService->setNewLanguage($userModel, $languageOrCloseArg);
                $userService->updateLanguage($userModel, $languageOrCloseArg);

                App::setLocale($languageOrCloseArg);

                $text = __("Language has changed to") . ' ' . $languageOrCloseArg;
                $historyMessageService->addRecord($text, $userModel->id, MessageType::info);

                $telegramService->sendMessage(
                    $chatId,
                    __("You have chosen") . ' ' .
                    $this->custom_lcfirst(config('language.for_bot_menu')[$languageOrCloseArg]) . '.'
                );
            } elseif ($languageOrCloseArg == 'close') {
                $telegramService->deleteMessage(
                    $chatId,
                    $menuCacheService->getLanguageMenuMessageIdAndFreeCache()
                );

                $telegramService->deleteMessage(
                    $chatId,
                    $menuCacheService->getCallLanguageMenuMessageIdAndFreeCache($chatId)
                );
            } else {
                $this->writeErrorLog('Try to select unavailable language', [
                    'tried' => $languageOrCloseArg
                ]);
                $telegramService->sendMessage($chatId, __("This language is temporary unavailable") . '.');
            }
        } else {

            /** @var $sentMessageId */
            $text = __("Choose a language") . ':' . PHP_EOL . PHP_EOL;
            $buttonsStruct = [];

            foreach (config('language.for_bot_menu') as $key => $language) {
                $button = [[
                    'text' => config('language.for_bot_menu')[$key], 'callback_data' => '#language ' . $key
                ]];
                array_push($buttonsStruct, $button);
            }

            array_push($buttonsStruct, [[
                'text' => __("Close"), 'callback_data' => '#language close'
            ]]);

            if (sizeof($buttonsStruct) != 0) {
                $telegramService->sendMessageAndButtons(
                    $chatId,
                    $text,
                    $buttonsStruct,
                    $sentMessageId
                );
                $menuCacheService->setLanguageMenuMessageId($sentMessageId);
            } else {
                $this->writeErrorLog('Languages are unavailable in bot menu');
            }
        }

        return response()->json(['ok' => 'Language select']);
    }

    private function custom_lcfirst(string $source, string $charSet = 'utf-8')
    {
        $firstChar = mb_strtolower(mb_substr($source, 0, 1, $charSet), $charSet);
        return $firstChar . mb_substr($source, 1, mb_strlen($source, $charSet), $charSet);
    }
}

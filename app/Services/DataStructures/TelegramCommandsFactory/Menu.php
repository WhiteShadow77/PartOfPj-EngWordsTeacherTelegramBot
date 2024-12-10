<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\Cache\MenuCacheService;
use App\Services\DataStructures\EnglishWordsSchedule\DayTimesSchedule;
use App\Services\DataStructures\EnglishWordsSchedule\WeekSchedule;
use App\Services\DataStructures\TelegramCommandsFactory\TelegramMenuCommandsFactory\TelegramMenuCommandFactory;
use App\Services\EnglishWordService;
use App\Services\History\HistoryMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Services\History\HistoryRecordService;
use App\Enums\SendScheduleKind;
use Illuminate\Support\Facades\App;

class Menu extends TelegramCommandFactory
{
    // use LoggerTrait;

    private WeekSchedule $weekSchedule;
    private DayTimesSchedule $dayTimesSchedule;
    private MenuCacheService $menuCacheService;

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
        $subMenu = isset($this->arguments[1]) ? $this->arguments[1] : null;
        $this->menuCacheService = new MenuCacheService($chatId);
        $messageId = $messageData ?->messageId ?? null;

        if (!is_null($messageId)) {
            $callMenuMessageId = $this->menuCacheService->getCallMenuMessageId();
            if (!is_null($callMenuMessageId)) {
                $telegramService->deleteMessage(
                    $chatId,
                    $this->menuCacheService->getCallMenuMessageIdAndFreeCache($chatId)
                );
            }

            $menuMessageId = $this->menuCacheService->getMenuMessageId();
            if (!is_null($menuMessageId)) {
                $telegramService->deleteMessage(
                    $chatId,
                    $this->menuCacheService->getMenuMessageIdAndCacheFree()
                );
            }

            $this->menuCacheService->setCallMenuMessageId($messageId);
        }

        if (!is_null($subMenu)) {
            $menuInstance = TelegramMenuCommandFactory::createCommand(
                $subMenu,
                $this->arguments
            );
            if ($menuInstance !== false) {
                $this->menuCacheService->setCurrentCommand($subMenu);
                $this->menuCacheService->setCurrentArguments($this->arguments);

                $menuInstance->run(
                    $userService,
                    $telegramService,
                    $messageId,
                    $chatId,
                    new WeekSchedule(),
                    new DayTimesSchedule(),
                    $this->menuCacheService,
                    $languageCacheService
                );
            } else {
                throw new \Exception(TelegramMenuCommandFactory::$errorMessage);
            }
        } else {
            $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
            App::setLocale($userLanguage);

            $text = __("What distribution to set up") . '?';
            $buttonsStruct = [
                [
                    [
                        'text' => __("Words"),
                        'callback_data' => '#menu Twitch',
                    ],
                    [
                        'text' => __("Quizzes"),
                        'callback_data' => '#menu Quiz',
                    ]
                ],
                [
//                    [
//                        'text' => __("Personal account"),
//                        'url' => config('app.profile_config_url'),
//                    ],
                    [
                        'text' => __("Quit"),
                        'callback_data' => '#menu MenuExit',
                    ]
                ]
            ];

            /** @var $messageId */
            $telegramService->sendMessageAndButtons(
                $chatId,
                $text,
                $buttonsStruct,
                $messageId
            );

            $this->menuCacheService->setMenuMessageId($messageId);
        }

        return response()->json(['ok' => 'Menu']);
    }
}

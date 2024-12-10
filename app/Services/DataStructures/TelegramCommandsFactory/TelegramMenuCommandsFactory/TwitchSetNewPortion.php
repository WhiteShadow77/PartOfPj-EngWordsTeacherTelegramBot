<?php

namespace App\Services\DataStructures\TelegramCommandsFactory\TelegramMenuCommandsFactory;

use App\Services\Cache\LanguageCacheService;
use App\Services\Cache\MenuCacheService;
use App\Services\DataStructures\EnglishWordsSchedule\DayTimesSchedule;
use App\Services\DataStructures\EnglishWordsSchedule\WeekSchedule;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Enums\SendScheduleKind;
use Illuminate\Support\Facades\App;

class TwitchSetNewPortion extends TelegramMenuCommandFactory
{
    public function run(
        UserService $userService,
        TelegramService $telegramService,
        ?string $messageId,
        string $chatId,
        weekSchedule $weekSchedule,
        DayTimesSchedule $dayTimesSchedule,
        MenuCacheService $menuCacheService,
        LanguageCacheService $languageCacheService
    ) {
        $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
        App::setLocale($userLanguage);

        $newPortion = $this->arguments[2];
        $userService->setCurrentEnglishWordsPortionByChatId($chatId, $newPortion);
        $menuCacheService->setConfigurableTwitchPortion($newPortion);

        $this->outputTwitchFinishSettingsMenu($chatId, $telegramService, $menuCacheService);
    }

    private function outputTwitchFinishSettingsMenu(
        string $chatId,
        TelegramService $telegramService,
        MenuCacheService $menuCacheService
    ) {
        $text = __("Word distribution setup completed successfully. Good luck with your studies") . hex2bin('F09F988A');
        $buttonsStruct = [[
            [
                'text' => __("Back"),
                'callback_data' => '#menu TwitchPortion',
            ],
            [
                'text' => __("Quit"),
                'callback_data' => '#menu MenuExit',
            ]
        ]];

        $telegramService->editMessageAndButtons(
            $chatId,
            $menuCacheService->getMenuMessageId(),
            $text,
            $buttonsStruct
        );
    }
}

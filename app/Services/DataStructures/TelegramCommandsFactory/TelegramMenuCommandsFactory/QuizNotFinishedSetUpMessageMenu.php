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

class QuizNotFinishedSetUpMessageMenu extends TelegramMenuCommandFactory
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

        $this->outputNotFinishedSetUpMessageMenu(
            $chatId,
            $telegramService,
            $userService,
            $weekSchedule,
            $menuCacheService
        );
    }

    private function outputNotFinishedSetUpMessageMenu(
        string $chatId,
        TelegramService $telegramService,
        UserService $userService,
        $weekSchedule,
        $menuCacheService
    ) {
        $previousArguments = $menuCacheService->getPreviousArguments();
        if (!is_null($previousArguments)) {
            $previousArgumentsFormated = ' ' . $previousArguments;
        } else {
            $previousArgumentsFormated = '';
        }

        $buttonsStruct = [[
            [
                'text' => __("Continue"),
                'callback_data' => '#menu ' . $menuCacheService->getPreviousCommand() . $previousArgumentsFormated,
            ],
            [
                'text' =>  __("Quit without saving"),
                'callback_data' =>  '#menu MenuExitWithoutSave' . ' ' . $menuCacheService->getConfigurableDay(),
            ],
        ]];
        $telegramService->editMessageAndButtons(
            $chatId,
            $menuCacheService->getMenuMessageId(),
            hex2bin('e29d97') .
            ' ' .
            __("The quiz distribution setup is not complete. To complete, continue the setup, otherwise the setup will not be saved") .
            '.',
            $buttonsStruct
        );
    }
}

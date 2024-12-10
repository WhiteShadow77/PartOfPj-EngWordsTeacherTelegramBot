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

class Twitch extends TelegramMenuCommandFactory
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

        $this->outputSendingDaysMenuButtons($chatId, $telegramService, $userService, $weekSchedule, $menuCacheService);
    }

    private function outputSendingDaysMenuButtons(
        string $chatId,
        TelegramService $telegramService,
        UserService $userService,
        $weekSchedule,
        $menuCacheService
    ) {
        $data = $weekSchedule->getSendingDaysFromUserForTelegramMenuByChatId(
            $chatId,
            SendScheduleKind::english_words
        );

        $daysScheduleFirstRow = [];
        $daysScheduleSecondRow = [];
        $buttonsStruct = [];
        $i = 0;

        foreach ($data['days'] as $day => $isChecked) {
            $i++;
            if ($isChecked) {
                $checkedDay = hex2bin('E29C85');
                $command = 'TwitchResetSendingDay';
            } else {
                $checkedDay = '';
                $command = 'TwitchSetSendingDay';
            }
            if ($i <= 4) {
                array_push($daysScheduleFirstRow, [
                        'text' =>  __($day) . ' ' . $checkedDay,
                        'callback_data' => '#menu ' . $command . ' ' . $day,
                    ]);
            } else {
                array_push($daysScheduleSecondRow, [
                        'text' => __($day) . ' ' . $checkedDay,
                        'callback_data' => '#menu ' . $command . ' ' . $day,
                    ]);
            }
        }

        $currentEnglishWordsPortion = $userService->getCurrentEnglishWordsQuantityByChatId($chatId);

        array_push($buttonsStruct, $daysScheduleFirstRow);
        array_push($buttonsStruct, $daysScheduleSecondRow);

        $twitchIsEnabled = $userService->getTwitchIsEnabledByChatId($chatId);

        if ($twitchIsEnabled) {
            $isEnabled = hex2bin('E29C85');
            $setIsEnabled = false;
        } else {
            $isEnabled =  hex2bin('F09F9AAB');
            $setIsEnabled = true;
        }

        array_push(
            $buttonsStruct,
            [
                [
                    'text' => __("Quantity") . ' ' . $currentEnglishWordsPortion,
                    'callback_data' => '#menu TwitchPortion ' . $currentEnglishWordsPortion
                ],
                [
                    'text' => __("Enabled") . ' ' . $isEnabled,
                    'callback_data' => '#menu TwitchSetIsEnabled ' . $setIsEnabled,
                ]
            ],
            [
                [
                    'text' => __("Back"),
                    'callback_data' => '#menu MainMenuRecreate',
                ],
                [
                    'text' => __("Quit"),
                    'callback_data' => '#menu MenuExit',
                ]
            ]
        );

        $telegramService->editMessageAndButtons(
            $chatId,
            $menuCacheService->getMenuMessageId(),
            __("Current words distribution") . ':',
            $buttonsStruct
        );
    }
}

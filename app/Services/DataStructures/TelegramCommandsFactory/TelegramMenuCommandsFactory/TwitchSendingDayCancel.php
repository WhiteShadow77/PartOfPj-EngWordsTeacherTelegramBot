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

class TwitchSendingDayCancel extends TelegramMenuCommandFactory
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
        /** @var $weekScheduleConfig */

        $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
        App::setLocale($userLanguage);

        $configurableDay = $this->arguments[2];

        $weekScheduleData = $weekSchedule->getFromUserByChatId(
            $chatId,
            SendScheduleKind::english_words,
            $weekScheduleConfig
        );

        $weekScheduleDays = $weekScheduleData['days'];
        $weekScheduleTimes = $weekScheduleData['times'];
        $dayTimesSchedule->setScheduleKind(SendScheduleKind::english_words);
        $weekSchedule->setWeekSchedule($weekScheduleConfig);

        foreach ($weekSchedule->getWeekTemplate() as $day => $dayCode) {
            if ($day == $configurableDay) {
                $weekSchedule->unsetSendingDayTime($day, SendScheduleKind::english_words);
            } else {
                $weekScheduleDay = current($weekScheduleDays);
                if ($weekScheduleDay === true) {
                    $dayTimesSchedule->setSendingTime(current($weekScheduleTimes));
                    $weekSchedule->setSendingDayTimeAndDayQuizQuantity(
                        $day,
                        $dayTimesSchedule
                    );
                }
            }
            next($weekScheduleDays);
            next($weekScheduleTimes);
        }
        $weekSchedule->saveToUserByChatId($chatId, SendScheduleKind::english_words);

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
                //$this->setSendingEnglishWordsDay($day, $chatId);
            } else {
                $checkedDay = '';
                $command = 'TwitchSetSendingDay';
                //$this->resetSendingEnglishWordsDay($day, $chatId);
            }
            if ($i <= 4) {
                array_push($daysScheduleFirstRow, [
                        'text' => $checkedDay . $day,
                        'callback_data' => '#menu ' . $command . ' ' . $day,
                    ]);
            } else {
                array_push($daysScheduleSecondRow, [
                        'text' => $checkedDay . $day,
                        'callback_data' => '#menu ' . $command . ' ' . $day,
                    ]);
            }
        }

        $twitchIsEnabled = $userService->getTwitchIsEnabledByChatId($chatId);

        if ($twitchIsEnabled) {
            $isEnabled = hex2bin('E29C85');
            $setIsEnabled = false;
        } else {
            $isEnabled = hex2bin('F09F9AAB');
            $setIsEnabled = true;
        }

        $currentEnglishWordsPortion = $userService->getCurrentEnglishWordsQuantityByChatId($chatId);
        array_push($daysScheduleSecondRow, [
            'text' => 'Portion ' . $currentEnglishWordsPortion,
            'callback_data' => '#menu TwitchPortion ' . $currentEnglishWordsPortion
        ]);

        array_push($buttonsStruct, $daysScheduleFirstRow);
        array_push($buttonsStruct, $daysScheduleSecondRow);

        array_push($buttonsStruct, [
            [
                'text' => 'Enabled ' . $isEnabled,
                'callback_data' => '#menu TwitchSetIsEnabled ' . $setIsEnabled,
            ]
        ]);

        array_push(
            $buttonsStruct,
            [
                [
                    'text' => __("Back"),
                    'callback_data' => '#menu Twitch',
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

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

class Quiz extends TelegramMenuCommandFactory
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

        $this->outputQuizSendingDaysMenuButtons(
            $chatId,
            $telegramService,
            $userService,
            $weekSchedule,
            $menuCacheService
        );
    }

    private function outputQuizSendingDaysMenuButtons(
        string $chatId,
        TelegramService $telegramService,
        UserService $userService,
        $weekSchedule,
        $menuCacheService
    ) {
        $quizIsEnabled = $userService->getQuizIsEnabledByChatId($chatId);
        $data = $weekSchedule->getSendingDaysFromUserForTelegramMenuByChatId(
            $chatId,
            SendScheduleKind::quiz
        );

        $daysScheduleFirstRow = [];
        $daysScheduleSecondRow = [];
        $buttonsStruct = [];
        $i = 0;

        foreach ($data['days'] as $day => $isChecked) {
            $i++;
            if ($isChecked) {
                $checkedDay = hex2bin('E29C85');
                $command = 'QuizResetSendingDay';
            } else {
                $checkedDay = '';
                $command = 'QuizSetSendingDay';
            }
            if ($i <= 4) {
                array_push($daysScheduleFirstRow, [
                        'text' => $checkedDay . __($day),
                        'callback_data' => '#menu ' . $command . ' ' . $day,
                    ]);
            } else {
                array_push($daysScheduleSecondRow, [
                        'text' => $checkedDay . __($day),
                        'callback_data' => '#menu ' . $command . ' ' . $day,
                    ]);
            }
        }
        if ($quizIsEnabled) {
            $buttonTitle = __('Enabled');
            $quizIsEnabledChecked = hex2bin('E29C85');
            $command = 'disable';
        } else {
            $buttonTitle = __('Disabled');
            $quizIsEnabledChecked = hex2bin('F09F9AAB');
            $command = 'enable';
        }

        array_push($buttonsStruct, $daysScheduleFirstRow);
        array_push($buttonsStruct, $daysScheduleSecondRow);

        array_push($buttonsStruct, [
                [
                    'text' => $buttonTitle . ' ' . $quizIsEnabledChecked,
                    'callback_data' => '#menu QuizSetIsEnabled ' . $command
                ]
            ]);
        array_push($buttonsStruct, [
                [
                    'text' => __("Back"),
                    'callback_data' => '#menu MainMenuRecreate',
                ],
                [
                    'text' => __("Quit"),
                    'callback_data' => '#menu MenuExit',
                ]
            ]);

        $telegramService->editMessageAndButtons(
            $chatId,
            $menuCacheService->getMenuMessageId(),
            __("Current quizzes distribution") . ':',
            $buttonsStruct
        );
    }
}

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

class QuizResetSendingDay extends TelegramMenuCommandFactory
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
        /** @var $daysAndTimesConfig */

        $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($chatId);
        App::setLocale($userLanguage);

        $selectedDay = $this->arguments[2];

        $dayTimesSchedule->setScheduleKind(SendScheduleKind::quiz);
        $weekSchedule->getFromUserByChatId(
            $chatId,
            SendScheduleKind::quiz,
            daysAndTimesConfigArray: $daysAndTimesConfig
        );

                $weekTemplate = $weekSchedule->getWeekTemplate();
                $selectedTime = $daysAndTimesConfig[$weekTemplate[$selectedDay]];
                $timesTemplate = $dayTimesSchedule->getTimesTemplate();

                $buttonsStruct = [];
                $timeButtonsRow = [];

                $flag = 0;

                $timeButtonsRow[] = [
                    'text' => __("Cancel"),
                    'callback_data' => '#menu QuizSendingDayCancel ' . $selectedDay,
                ];

                $colQuantity = 4;

                foreach ($timesTemplate as $time) {
                    $checkedTime = '';
                    if ($time == $selectedTime) {
                        $checkedTime = hex2bin('E29C85');
                    }
                    if ($flag == 0) {
                        $flag = 1;
                        $i = 2;
                    }
                    if ($i % $colQuantity == $colQuantity - 1) {
                        $timeButtonsRow[] = [
                            'text' => $checkedTime . $time,
                            'callback_data' => '#menu QuizSendingDaySetTime ' . $time,
                        ];
                        array_push($buttonsStruct, $timeButtonsRow);
                        $timeButtonsRow = [];
                    } else {
                        $timeButtonsRow[] = [
                            'text' => $checkedTime . $time,
                            'callback_data' => '#menu QuizSendingDaySetTime ' . $time,
                        ];
                    }
                    if ($i == 4 * 24) {
                        break;
                    }
                    $i++;
                }
                array_push(
                    $buttonsStruct,
                    [
                        [
                            'text' => __("Back"),
                            'callback_data' => '#menu Quiz',
                        ],
                        [
                            'text' => __("Quit"),
                            'callback_data' => '#menu MenuExit',
                        ],
                        [
                            'text' => __("Cancel"),
                            'callback_data' => '#menu QuizSendingDayCancel ' . $selectedDay,
                        ]
                    ]
                );

                $menuCacheService->setConfigurableDay($selectedDay);

                $telegramService->editMessageAndButtons(
                    $chatId,
                    $menuCacheService->getMenuMessageId(),
                    __("Choose a time for") . ' ' . __("sending out the words") . ' ' .
                    __("messages.in_day." . $selectedDay) . ":",
                    $buttonsStruct
                );
    }
}

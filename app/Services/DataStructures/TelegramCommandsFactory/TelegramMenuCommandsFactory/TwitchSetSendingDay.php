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

class TwitchSetSendingDay extends TelegramMenuCommandFactory
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

        $selectedDay = $this->arguments[2];

        $dayTimesSchedule->setScheduleKind(SendScheduleKind::english_words);

        $buttonsStruct = [];
        $timeButtonsRow = [];
        $i = 0;

        foreach ($dayTimesSchedule->getTimesTemplate() as $time) {
            $i++;
            if ($i % 4) {
                $timeButtonsRow[] = [
                    'text' => $time,
                    'callback_data' => '#menu TwitchSendingDaySetTime ' . $time,
                ];
            } else {
                $timeButtonsRow[] = [
                    'text' => $time,
                    'callback_data' => '#menu TwitchSendingDaySetTime ' . $time,
                ];
                array_push($buttonsStruct, $timeButtonsRow);
                $timeButtonsRow = [];
            }
            if ($i == 4 * 24) {
                break;
            }
        }
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

        $menuCacheService->setConfigurableDay($selectedDay);

        $telegramService->editMessageAndButtons(
            $chatId,
            $menuCacheService->getMenuMessageId(),
            __("Choose a time for") . ' ' . __("sending out the words") . ' ' .
            __("messages.in_day." . $selectedDay) . ':',
            $buttonsStruct
        );
    }
}

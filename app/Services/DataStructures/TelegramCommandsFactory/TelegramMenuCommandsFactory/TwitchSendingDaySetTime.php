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

class TwitchSendingDaySetTime extends TelegramMenuCommandFactory
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

        $configurableDay = $menuCacheService->getConfigurableDay();
        $configurableTime = $this->arguments[2];

        /** @var $weekScheduleConfig */
        /** @var $daysAndQuizQuantityConfig */
        /** @var $dayAndTimesConfig */

        $weekScheduleData = $weekSchedule->getFromUserByChatId(
            $chatId,
            SendScheduleKind::english_words,
            $weekScheduleConfig,
            $dayAndTimesConfig,
            $daysAndQuizQuantityConfig
        );

        $weekScheduleDays = $weekScheduleData['days'];
        $weekScheduleTimes = $weekScheduleData['times'];
        $dayTimesSchedule->setScheduleKind(SendScheduleKind::english_words);

        $weekSchedule->setWeekSchedulWithQuizQuantities($daysAndQuizQuantityConfig);

        foreach ($weekSchedule->getWeekTemplate() as $day => $dayCode) {
            if ($day == $configurableDay) {
                $dayTimesSchedule->setSendingTime($configurableTime);
                $weekSchedule->setSendingDayTimeAndDayQuizQuantity(
                    $day,
                    $dayTimesSchedule
                );
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
        $this->outputTwitchFinishSettingsMenu($chatId, $telegramService, $menuCacheService, $userService);
    }

    private function outputTwitchFinishSettingsMenu(
        string $chatId,
        TelegramService $telegramService,
        MenuCacheService $menuCacheService,
        UserService $userService
    ) {
        $twitchIsEnabled = $userService->getTwitchIsEnabledByChatId($chatId);

        if ($twitchIsEnabled) {
            $text = __("Word distribution setup completed successfully. Good luck with your studies") . hex2bin('F09F988A');
            $buttonsStruct = [[
                [
                    'text' => __("Back"),
                    'callback_data' => '#menu TwitchResetSendingDay ' . $menuCacheService->getConfigurableDay(),
                ],
                [
                    'text' => __("Quit"),
                    'callback_data' => '#menu MenuExit',
                ]
            ]];
        } else {
            $text = __("Word distribution setup completed successfully, but distribution is disabled. To enable, go to distribution menu") . '.';
            $buttonsStruct = [[
                [
                    'text' => __("Distribution menu"),
                    'callback_data' => '#menu Twitch',
                ],
                [
                    'text' => __("Back"),
                    'callback_data' => '#menu TwitchResetSendingDay ' . $menuCacheService->getConfigurableDay(),
                ],
                [
                    'text' => __("Quit"),
                    'callback_data' => '#menu MenuExit',
                ]
            ]];
        }

        $telegramService->editMessageAndButtons(
            $chatId,
            $menuCacheService->getMenuMessageId(),
            $text,
            $buttonsStruct
        );
    }
}

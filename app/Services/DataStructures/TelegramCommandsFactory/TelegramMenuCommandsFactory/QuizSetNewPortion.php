<?php

namespace App\Services\DataStructures\TelegramCommandsFactory\TelegramMenuCommandsFactory;

use App\Services\Cache\LanguageCacheService;
use App\Services\Cache\MenuCacheService;
use App\Services\DataStructures\EnglishWordsSchedule\DayQuizQuantitySchedule;
use App\Services\DataStructures\EnglishWordsSchedule\DayTimesSchedule;
use App\Services\DataStructures\EnglishWordsSchedule\WeekSchedule;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Enums\SendScheduleKind;
use Illuminate\Support\Facades\App;

class QuizSetNewPortion extends TelegramMenuCommandFactory
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

        $quizPortion = $this->arguments[2];

        $dayQuizQuantitySchedule = new DayQuizQuantitySchedule();
        $configurableDay = $menuCacheService->getConfigurableDay();
        $configurableTime = $menuCacheService->getConfigurableTime();

        /** @var $weekScheduleConfig */
        /** @var $daysAndQuizQuantityConfig */
        /** @var $dayAndTimesConfig */
        $weekSchedule->getFromUserByChatId(
            $chatId,
            SendScheduleKind::quiz,
            $weekScheduleConfig,
            $dayAndTimesConfig,
            $daysAndQuizQuantityConfig
        );

        $dayTimesSchedule->setScheduleKind(SendScheduleKind::quiz);
        $weekSchedule->setWeekSchedule($weekScheduleConfig);
        $weekSchedule->setWeekScheduleWithTimes($dayAndTimesConfig);
        $weekSchedule->setWeekSchedulWithQuizQuantities($daysAndQuizQuantityConfig);

        $dayTimesSchedule->setSendingTime($configurableTime);

        $weekSchedule->setSendingDayTimeAndDayQuizQuantity(
            $configurableDay,
            $dayTimesSchedule,
            $dayQuizQuantitySchedule->setQuantity($quizPortion)
        );

        $weekSchedule->saveToUserByChatId($chatId, SendScheduleKind::quiz);

        $quizIsEnabled = $userService->getQuizIsEnabledByChatId($chatId);

        if ($quizIsEnabled) {
            $text = __("Quiz distribution setup completed successfully. Good luck with your studies") . hex2bin('F09F988A');
            $buttonsStruct[] = [
                [
                    'text' => __("Back"),
                    'callback_data' => '#menu QuizRecreateSetNewPortion',
                ], [
                    'text' => __("Quit"),
                    'callback_data' => '#menu MenuExit',
                ]
            ];
        } else {
            $text = 'Настройка рассылки тестов успешно завершена. Но рассылка выключена. Для включения перейди в меню рассылок.';
            $buttonsStruct[] = [
                [
                    'text' => 'Перейти в меню рассылок',
                    'callback_data' => '#menu Quiz',
                ]
            ];
            $buttonsStruct[] =  [
                [
                    'text' => __("Back"),
                    'callback_data' => '#menu QuizRecreateSetNewPortion',
                ],
                [
                    'text' => __("Quit"),
                    'callback_data' => '#menu MenuExit',
                ]
            ];
        }

        $telegramService->editMessageAndButtons(
            $chatId,
            $menuCacheService->getMenuMessageId(),
            $text,
            $buttonsStruct
        );
    }
}

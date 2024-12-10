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

class QuizRecreateSetNewPortion extends TelegramMenuCommandFactory
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

        $this->outputQuizSetPortionMenuButtons(
            $chatId,
            $menuCacheService->getConfigurableDay(),
            $telegramService,
            $userService,
            $weekSchedule,
            $menuCacheService
        );
    }

    private function outputQuizSetPortionMenuButtons(
        string $chatId,
        string $configurableDay,
        TelegramService $telegramService,
        UserService $userService,
        $weekSchedule,
        $menuCacheService
    ) {
        $data = $weekSchedule->getFromUserByChatId(
            $chatId,
            SendScheduleKind::quiz
        );

        $quizQuantityForConfigurableDay = isset($data['quiz_quantities'][$configurableDay])
            ? $data['quiz_quantities'][$configurableDay]
            : null;

        $quizAvailableQuantity = $userService->getQuizAvailableQuantity();

        $buffer = [];
        for ($quizQuantity = 1; $quizQuantity <= $quizAvailableQuantity; $quizQuantity++) {
            if ($quizQuantity == $quizQuantityForConfigurableDay) {
                $checked = hex2bin('E29C85');
            } else {
                $checked = '';
            }
            if ($quizQuantity % 7) {
                $buffer[] = [
                    'text' => $quizQuantity . ' ' . $checked,
                    'callback_data' => '#menu QuizSetNewPortion' . ' ' . $quizQuantity,
                ];
            } else {
                $buffer[] = [
                    'text' => $quizQuantity . ' ' . $checked,
                    'callback_data' => '#menu QuizSetNewPortion' . ' ' . $quizQuantity,
                ];
                $buttonsStruct[] = $buffer;
                $buffer = [];
            }
        }

        $buttonsStruct[] = $buffer;
        $buttonsStruct[] = [
            [
                'text' => __("Back"),
                'callback_data' => '#menu QuizResetSendingDay ' . $configurableDay,
            ],
            [
                'text' => __("Quit"),
                'callback_data' => '#menu MenuExit',
            ]
        ];

        $telegramService->editMessageAndButtons(
            $chatId,
            $menuCacheService->getMenuMessageId(),
            __("Current number of tests") . ':',
            $buttonsStruct
        );
    }
}

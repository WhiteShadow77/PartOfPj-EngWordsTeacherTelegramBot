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

class QuizSetSendingDayPortion extends TelegramMenuCommandFactory
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

    $selectedPortion = $this->arguments[2];
    $this->outputCurrentQuizPortionMenu(
        $chatId,
        $telegramService,
        $userService,
        $selectedPortion,
        $menuCacheService
    );
}

private function outputCurrentQuizPortionMenu(
    string $chatId,
    TelegramService $telegramService,
    UserService $userService,
    string $selectedPortion,
    MenuCacheService $menuCacheService
) {
    $buttonsStruct = [];
    $buffer = [];
    $text = 'Portion of the quiz for day';
    $i = $userService->getEnglishWordsMinAvailableQuantity();
    $iMax = $userService->getEnglishWordsMaxAvailableQuantity();

    for ($i; $i <= $iMax; $i++) {
        if ($i == $selectedPortion) {
            $checked = hex2bin('E29C85');
        } else {
            $checked = '';
        }

        if ($i % 8 < 7) {
            $buffer[] = [
                'text' => $i . ' ' . $checked,
                'callback_data' => '#menu QuizSetNewPortion ' . $i,
            ];
        } else {
            $buttonsStruct[] = $buffer;
            $buffer = [];
        }
    }

    $buttonsStruct[] = $buffer;
    $buttonsStruct[] = [
        [
            'text' => __("Back"),
            'callback_data' => '#menu QuizSendingDaySetTime ' . $menuCacheService->getConfigurableTime(),
        ], [
            'text' => __("Quit"),
            'callback_data' => '#menu MenuExit',
        ]
    ];

    $telegramService->editMessageAndButtons(
        $chatId,
        $menuCacheService->getMenuMessageId(),
        $text,
        $buttonsStruct
    );
}

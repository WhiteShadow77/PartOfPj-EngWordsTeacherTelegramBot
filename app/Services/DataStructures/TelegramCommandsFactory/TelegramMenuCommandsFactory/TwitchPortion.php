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

class TwitchPortion extends TelegramMenuCommandFactory
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

        if (isset($this->arguments[2]) && $this->arguments[2] !== false) {
            $selectedPortion = $this->arguments[2];
            $menuCacheService->setConfigurableTwitchPortion($selectedPortion);
        } else {
            $selectedPortion = $menuCacheService->getConfigurableTwitchPortion();
        }

        $this->outputCurrentEnglishWordsPortionMenu(
            $chatId,
            $telegramService,
            $userService,
            $selectedPortion,
            $menuCacheService
        );
    }

    private function outputCurrentEnglishWordsPortionMenu(
        string $chatId,
        TelegramService $telegramService,
        UserService $userService,
        string $selectedPortion,
        MenuCacheService $menuCacheService
    ) {
        $buttonsStruct = [];
        $buffer = [];
        $text = __("Quantity of words in the distribution") . ':';
        $i = $userService->getEnglishWordsMinAvailableQuantity();
        $iMax = $userService->getEnglishWordsMaxAvailableQuantity();

        for ($i; $i <= $iMax; $i++) {
            if ($i == $selectedPortion) {
                $checked = hex2bin('E29C85');
            } else {
                $checked = '';
            }

            if ($i % 6) {
                $buffer[] = [
                    'text' => $i . ' ' . $checked,
                    'callback_data' => '#menu TwitchSetNewPortion ' . $i,
                ];
            } else {
                $buffer[] = [
                    'text' => $i . ' ' . $checked,
                    'callback_data' => '#menu TwitchSetNewPortion ' . $i,
                ];
                $buttonsStruct[] = $buffer;
                $buffer = [];
            }
        }

        $buttonsStruct[] = $buffer;
        $buttonsStruct[] = [
            [
                'text' => __("Back"),
                'callback_data' => '#menu Twitch',
            ],
            [
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
}

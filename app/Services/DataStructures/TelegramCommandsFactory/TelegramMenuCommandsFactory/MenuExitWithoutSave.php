<?php

namespace App\Services\DataStructures\TelegramCommandsFactory\TelegramMenuCommandsFactory;

use App\Services\Cache\LanguageCacheService;
use App\Services\Cache\MenuCacheService;
use App\Services\DataStructures\EnglishWordsSchedule\DayTimesSchedule;
use App\Services\DataStructures\EnglishWordsSchedule\WeekSchedule;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Enums\SendScheduleKind;

class MenuExitWithoutSave extends TelegramMenuCommandFactory
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
        $configurableDay = $this->arguments[2];

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

        $weekSchedule->unsetSendingDayTime($configurableDay, SendScheduleKind::quiz);

        $weekSchedule->saveToUserByChatId($chatId, SendScheduleKind::quiz);

        $telegramService->deleteMessage(
            $chatId,
            $menuCacheService->getMenuMessageIdAndCacheFree()
        );

        $telegramService->deleteMessage(
            $chatId,
            $menuCacheService->getCallMenuMessageId($chatId)
        );

        $menuCacheService->freeMenuCache();
    }
}

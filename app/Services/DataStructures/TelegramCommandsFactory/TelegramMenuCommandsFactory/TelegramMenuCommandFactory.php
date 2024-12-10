<?php

namespace App\Services\DataStructures\TelegramCommandsFactory\TelegramMenuCommandsFactory;

use App\Services\Cache\LanguageCacheService;
use App\Services\Cache\MenuCacheService;
use App\Services\DataStructures\EnglishWordsSchedule\DayTimesSchedule;
use App\Services\DataStructures\EnglishWordsSchedule\WeekSchedule;
use App\Services\Helpers\Logger;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Traits\LoggerTrait;

abstract class TelegramMenuCommandFactory
{
    use LoggerTrait;

    public static string $errorMessage = '';

    protected ?array $arguments;
    protected ?string $messageId;
    protected ?string $language = null;

    public static function createCommand(
        string $commandName,
        ?array $arguments = null
    ) {
        $commandClass = __NAMESPACE__ . '\\' . ucfirst($commandName);
        if (class_exists($commandClass)) {
            $commandInstance = new $commandClass();

            if (is_null($arguments)) {
                self::$errorMessage = 'No arguments';

                Logger::writeErrorLog('Instance has not created in the Telegram menu command factory', [
                    'error user message' => self::$errorMessage,
                ]);

                return false;
            }
            $commandInstance->arguments = $arguments;

            Logger::writeInfoLog('Instance created in the Telegram menu command factory', [
                'instance' => get_class($commandInstance),
            ]);
            return $commandInstance;
        } else {
            self::$errorMessage = 'Menu command class ' . $commandName . ' not found';

            Logger::writeErrorLog('Instance has not created in the Telegram menu command factory', [
                'error message' => self::$errorMessage,
            ]);
            return false;
        }
    }

    abstract public function run(
        UserService $userService,
        TelegramService $telegramService,
        ?string $messageId,
        string $chatId,
        WeekSchedule $weekSchedule,
        DayTimesSchedule $dayTimesSchedule,
        MenuCacheService $menuCacheService,
        LanguageCacheService $languageCacheService
    );
}

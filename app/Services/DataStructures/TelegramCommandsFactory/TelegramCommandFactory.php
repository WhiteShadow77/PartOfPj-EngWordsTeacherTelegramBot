<?php

namespace App\Services\DataStructures\TelegramCommandsFactory;

use App\Services\Auth\AuthRedisService;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\Helpers\Logger;
use App\Services\History\HistoryRecordService;
use App\Services\History\HistoryMessageService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Traits\LoggerTrait;

abstract class TelegramCommandFactory
{
    use LoggerTrait;

    public static string $errorUserMessage = '';
    public static string $errorResponseMessage = '';
    protected ?array $arguments;
    protected ?string $messageId;
    protected ?string $language = null;

    public static function createCommand(
        string $commandName,
        ?array $arguments = null,
        ?string $messageId = null,
        array $commandNamesWithoutArgQuantityLimit = []
    ) {
        $commandClass = __NAMESPACE__ . '\\' . ucfirst($commandName);
        if (class_exists($commandClass)) {
            $commandInstance = new $commandClass();
            $studyWordsLimitQuantity = config('study.word_limit_quantity');

            if (
                !is_null($arguments) &&
                count($arguments) > $studyWordsLimitQuantity &&
                !in_array($commandName, $commandNamesWithoutArgQuantityLimit)
            ) {
                self::$errorUserMessage = 'Количество слов не должно быть больше ' . $studyWordsLimitQuantity . '.';
                self::$errorResponseMessage = 'Quantity beyond limit';

                Logger::writeErrorLog('Instance has not created in the Telegram command factory', [
                    'error user message' => self::$errorUserMessage,
                    'error response message' => self::$errorResponseMessage,
                ]);

                return false;
            }
            $commandInstance->arguments = $arguments;
            $commandInstance->messageId = $messageId;

            Logger::writeInfoLog('Instance created in the Telegram command factory', [
                'instance' => get_class($commandInstance),
            ]);
            return $commandInstance;
        } else {
            self::$errorUserMessage = 'Неизвестная мне команда.';
            self::$errorResponseMessage = 'Bad command';

            Logger::writeErrorLog('Instance has not created in the Telegram command factory', [
                'error user message' => self::$errorUserMessage,
                'error response message' => self::$errorResponseMessage,
            ]);
            return false;
        }
    }

    /** Parses all arguments. If it argument has symbol '=' and word before is equal to key returns word after the '='.
     * If has not found '=' returns false
     * @param string $key
     * @return bool
     */
    public function parseArgumentsByKey(string $key)
    {
        foreach ($this->arguments as $argument) {
            $item = explode('=', $argument);
            if ($item[0] == $key) {
                return $item[1];
            }
        }
        return false;
    }

    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    abstract public function run(
        UserService $userService,
        HistoryRecordService $historyRecordService,
        HistoryMessageService $historyMessageService,
        ?object $messageData,
        AuthRedisService $authRedisService,
        TelegramService $telegramService,
        EnglishWordService $englishWordService,
        string $chatId,
        LanguageCacheService $languageCacheService
    );
}

<?php

namespace App\Services\Log;

use App\Models\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Enums\LogType;

class LoggerService
{
    private function getRootDir(int $level = 3): string
    {
        $foldersOfCatalog = explode('/', __DIR__);
        for ($i = 1; $i <= $level; $i++) {
            end($foldersOfCatalog);
            unset($foldersOfCatalog[key($foldersOfCatalog)]);
        }

        return implode('/', $foldersOfCatalog);
    }

    public function log(
        string $level,
        string $description,
        string $action,
        array $data = [],
        $file = null,
        $line = null
    ): void {
        $actionArray = ["action" => $action];
        $mergedData = is_array($data) ? array_merge($actionArray, $data) : $actionArray;

        $logData = [
            "timestamp" => now()->toDateTimeString(),
            "level" => $level,
            "description" => $description,
            "data" => $mergedData,
        ];

        if ($file) {
            $logData["file"] = $file;
        }

        if ($line) {
            $logData["line"] = $line;
        }

        $isEnabledLogWrite = Cache::get('is_enabled_log_write', function () {
            $result = Log::first()->is_enabled_write;
            Cache::put('is_enabled_log_write', $result, now()->addHours(
                config('logging.log_config_pull_from_cache_life_time_in_hours')
            ));
            return $result;
        });

        if ($isEnabledLogWrite) {
            file_put_contents(
                $this->getRootDir() . '/storage/logs/app.log',
                json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND
            );
            //Log::{$level}(json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }

    public function writeInfoLog(
        string $description,
        array $data = [],
        object $loggerableObject,
        string $action = null,
        bool $isAllowedSendToTlg = false
    ): void {
        $this->writeLog($description, $data, $loggerableObject, LogType::info, $action, $isAllowedSendToTlg);
    }

    public function writeErrorLog(
        string $description,
        array $data = [],
        object $loggerableObject,
        string $action = null,
        bool $isAllowedSendToTlg = false
    ): void {
        $this->writeLog($description, $data, $loggerableObject, LogType::error, $action, $isAllowedSendToTlg);
    }

    private function writeLog(
        string $description,
        array $data = [],
        object $loggerableObject,
        LogType $logType,
        string $action = null,
        bool $isAllowedSendToTlg = false
    ) {
        $instance = [];
        $className = get_class($loggerableObject);
        if (is_null($action)) {
            $instance ['action'] = $className . '\\' . get_class_methods($className)[0];
        } else {
            $instance ['action'] = $action;
        }
        $instance['obj id'] = spl_object_id($loggerableObject);
        $logData = [
            "timestamp" => now()->toDateTimeString(),
            "level" => $logType->name,
            "instance" => $instance,
            "description" => $description,
            "data" => $data,
            "logger obj id" => spl_object_id($this)
        ];

        $isEnabledLogWrite = Cache::get('is_enabled_log_write', function () {
            $result = Log::first()->is_enabled_write;
            Cache::put('is_enabled_log_write', $result, now()->addHours(
                config('logging.log_config_pull_from_cache_life_time_in_hours')
            ));
            return $result;
        });
        if ($isEnabledLogWrite) {
            file_put_contents(
                $this->getRootDir() . '/storage/logs/app.log',
                json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND
            );
        }

        if ($isAllowedSendToTlg) {
            $isEnabledLogSend = Cache::get('is_enabled_log_send', function () {
                $result = Log::first()->is_enabled_send;
                Cache::put('is_enabled_log_send', $result, now()->addHours(
                    config('logging.log_config_pull_from_cache_life_time_in_hours')
                ));
                return $result;
            });
            if ($isEnabledLogSend) {
                if (isset($logData['timestamp'])) {
                    $timestampBuffer = $logData['timestamp'];
                    unset($logData['timestamp']);
                    $logData['timestamp'] = $timestampBuffer;
                }
                $logData['logger obj id'] = spl_object_id($this);
                $message = 'Notification: ' . PHP_EOL;
                array_walk_recursive($logData, function ($item, $key) use (&$message) {
                    $message .= '- ' . $key . ': ' . $item . PHP_EOL;
                });
                $message .= PHP_EOL;

                $chatId = config('bot.telegram_admin_id'); //admin id
                $uri = config('bot.send_message_to_bot_uri');

                $request = [
                    "chat_id" => $chatId,
                    "text" => $message,
                    "parse_mode" => "markdown",
                ];

                try {
                    Http::get($uri, $request);
                } catch (\Exception $exception) {
                }
            }
        }
    }
}

<?php

namespace App\Traits;

use App\Models\Log;
use App\Services\Log\LogConfigService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use App\Enums\LogType;

trait LoggerTrait
{
    private function loggerTraitGetRootDir(): string
    {
        $foldersOfCatalog = explode('/', __DIR__);
        for ($i = 1; $i <= 2; $i++) {
            end($foldersOfCatalog);
            unset($foldersOfCatalog[key($foldersOfCatalog)]);
        }

        return implode('/', $foldersOfCatalog);
    }

    public function writeInfoLog(
        string $description,
        array $data = [],
        string $action = null,
        bool $isAllowedSendToTlg = false
    ): void {
        $this->writeLog($description, $data, LogType::info, $action, $isAllowedSendToTlg);
    }

    public function writeErrorLog(
        string $description,
        array $data = [],
        string $action = null,
        bool $isAllowedSendToTlg = false
    ): void {
        $this->writeLog($description, $data, LogType::error, $action, $isAllowedSendToTlg);
    }

    private function writeLog(
        string $description,
        array $data = [],
        LogType $logType,
        string $action = null,
        bool $isAllowedSendToTlg = false
    ) {
        $logConfigService = App::make(LogConfigService::class);
        $className = get_class($this);

        $instance = [];
        if (!is_null($action)) {
            $instance['action'] = $action;
        }
        $instance['class'] = $className;
        $instance['obj id'] = spl_object_id($this);

        $logData = [
            "timestamp" => now()->toDateTimeString(),
            "level" => $logType->name,
            "instance" => $instance,
            "description" => $description,
            "data" => $data,
            "log config obj id" => $logConfigService->getObjId(),
        ];

        $isEnabledLogWrite = $logConfigService->getIsEnabledLogWrite();

        if (is_null($isEnabledLogWrite)) {
            $isEnabledLogWrite = Log::first()->is_enabled_write;
            $logConfigService->setIsEnabledLogWrite($isEnabledLogWrite);
        }
        if ($isEnabledLogWrite) {
            file_put_contents(
                $this->loggerTraitGetRootDir() . '/storage/logs/app.log',
                json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND
            );
        }

        if ($isAllowedSendToTlg) {
            $isEnabledLogSend = $logConfigService->getIsEnabledLogSend();
            if (is_null($isEnabledLogSend)) {
                $isEnabledLogSend = Log::first()->is_enabled_send;
                $logConfigService->setIsEnabledLogSend($isEnabledLogSend);
            }
            if ($isEnabledLogSend) {
                if (isset($logData['timestamp'])) {
                    $timestampBuffer = $logData['timestamp'];
                    unset($logData['timestamp']);
                    $logData['timestamp'] = $timestampBuffer;
                }
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

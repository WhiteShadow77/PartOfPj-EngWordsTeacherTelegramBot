<?php

namespace App\Services\Cache;

use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class MenuCacheService
{
    use LoggerTrait;

    public string $chatId;

    public function __construct(string $chatId)
    {
        $this->chatId = $chatId;
    }

    public function setCurrentArguments(array $arguments)
    {
        unset($arguments[1]); //delete of the commend name

        if (sizeof($arguments) > 0) {
            $argumentsAsStringOrNull = implode(' ', $arguments);
        } else {
            $argumentsAsStringOrNull = null;
        }

        Redis::lPush($this->chatId . '_user_menu_arguments_list', $argumentsAsStringOrNull);
        Redis::lTrim($this->chatId . '_user_menu_arguments_list', 0, 1);
    }

    public function setCurrentCommand(string $command)
    {
        Redis::lPush($this->chatId . '_user_menu_commands_list', $command);
        Redis::lTrim($this->chatId . '_user_menu_commands_list', 0, 1);
    }

    public function getPreviousCommand()
    {
        return Redis::lIndex($this->chatId . '_user_menu_commands_list', 1);
    }

    public function getPreviousArguments()
    {
        $result = Redis::lIndex($this->chatId . '_user_menu_arguments_list', 1);
        if (!is_null($result)) {
            return Redis::lIndex($this->chatId . '_user_menu_arguments_list', 1);
        } else {
            return null;
        }
    }

    public function setConfigurableQuizPortion(string $day)
    {
        Cache::put($this->chatId . '_user_configurable_quiz_portion', $day);
    }

    public function getConfigurableQuizPortion()
    {
        return Cache::get($this->chatId . '_user_configurable_quiz_portion');
    }

    public function setConfigurableDay(string $day)
    {
        Cache::put($this->chatId . '_user_configurable_day', $day);
    }

    public function getConfigurableDayAndFreeCache()
    {
        return Cache::pull($this->chatId . '_user_configurable_day');
    }

    public function getConfigurableDay()
    {
        return Cache::get($this->chatId . '_user_configurable_day');
    }

    public function setConfigurableTime(string $time)
    {
        Cache::put($this->chatId . '_user_configurable_time', $time);
    }

    public function getConfigurableTimeAndFreeCache()
    {
        return Cache::pull($this->chatId . '_user_configurable_time');
    }

    public function getConfigurableTime()
    {
        return Cache::get($this->chatId . '_user_configurable_time');
    }

    public function setCallMenuMessageId(int $messageId)
    {
        Cache::put($this->chatId . '_user_call_menu_message_id', $messageId);
    }

    public function getCallMenuMessageId()
    {
        return Cache::get($this->chatId . '_user_call_menu_message_id');
    }

    public function getCallMenuMessageIdAndFreeCache()
    {
        return Cache::pull($this->chatId . '_user_call_menu_message_id');
    }

    public function setCallLanguageMenuMessageId(int $messageId)
    {
        Cache::put($this->chatId . '_user_call_language_menu_message_id', $messageId);
    }

    public function getCallHelpMenuMessageId()
    {
        return Cache::get($this->chatId . '_user_call_help_menu_message_id');
    }

    public function getCallHelpMenuMessageIdAndFreeCache()
    {
        return Cache::pull($this->chatId . '_user_call_help_menu_message_id');
    }

    public function setCallHelpMenuMessageId(int $messageId)
    {
        Cache::put($this->chatId . '_user_call_help_menu_message_id', $messageId);
    }

    public function getHelpMenuMessageId()
    {
        return Cache::get($this->chatId . '_user_help_menu_message_id');
    }

    public function getHelpMenuMessageIdAndCacheFree()
    {
        return Cache::pull($this->chatId . '_user_help_menu_message_id');
    }

    public function setHelpMenuMessageId(int $messageId)
    {
        Cache::put($this->chatId . '_user_help_menu_message_id', $messageId);
    }

    public function getCallLanguageMenuMessageIdAndFreeCache()
    {
        return Cache::pull($this->chatId . '_user_call_language_menu_message_id');
    }

    public function setConfigurableTwitchPortion(int $portion)
    {
        Cache::put($this->chatId . '_user_configurable_twitch_portion', $portion);
    }

    public function getConfigurableTwitchPortion()
    {
        return Cache::get($this->chatId . '_user_configurable_twitch_portion');
    }

    public function getMenuMessageId()
    {
        return Cache::get($this->chatId . '_user_menu_message_id');
    }

    public function setMenuMessageId(string $messageId)
    {
        Cache::put($this->chatId . '_user_menu_message_id', $messageId);
    }

    public function getMenuMessageIdAndCacheFree()
    {
        return Cache::pull($this->chatId . '_user_menu_message_id');
    }

    public function setLanguageMenuMessageId(string $messageId)
    {
        Cache::put($this->chatId . '_user_language menu_message_id', $messageId);
    }

    public function getLanguageMenuMessageIdAndFreeCache()
    {
        return Cache::pull($this->chatId . '_user_language menu_message_id');
    }

    public function freeMenuCache()
    {
        Cache::forget($this->chatId . '_user_configurable_quiz_portion');
        Cache::forget($this->chatId . '_user_configurable_day');
        Cache::forget($this->chatId . '_user_configurable_time');
        Cache::forget($this->chatId . '_user_call_menu_message_id');
        Cache::forget($this->chatId . '_user_configurable_twitch_portion');
        Cache::forget($this->chatId . '_user_menu_message_id');
        Redis::del($this->chatId . '_user_menu_commands_list');
        Redis::del($this->chatId . '_user_menu_commands_list');
    }
}

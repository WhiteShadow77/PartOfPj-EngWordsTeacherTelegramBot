<?php

namespace App\Services\Auth;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Services\UserService;
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\Redis;

class AuthRedisService
{
    use LoggerTrait;

    public function addTelegramUserId(string $telegramUserId): void
    {
        Redis::set($telegramUserId, $telegramUserId);
        $this->writeInfoLog('Added to redis set key/value', [
            'key' => $telegramUserId,
            'value' => $telegramUserId,
        ]);
    }

    public function isTelegramUserIdExists(string $telegramUserId): bool
    {
        $result = !is_null(Redis::get($telegramUserId));
        $this->writeInfoLog('Checked is key/value exists in redis set', [
            'is exists' => $result,
        ]);
        return $result;
    }

    public function deleteValueByTelegramUserId(string $telegramUserId): void
    {
        Redis::del($telegramUserId);
        $this->writeInfoLog('Deleted key/value from redis set', [
            'key' => $telegramUserId,
            'value' => $telegramUserId,
        ]);
    }

    public function authorize(string $telegramUserId, UserService $userService): bool
    {
        if ($this->isTelegramUserIdExists($telegramUserId)) {
            $result = true;
            $this->writeInfoLog('User record exists in redis set', [
                'telegram user id' => $telegramUserId,
                'result' => $result,
            ]);
        } else {
            $userModel = $userService->getUserByTelegramUserId($telegramUserId);
            if (!is_null($userModel)) {
                $this->addTelegramUserId($telegramUserId);
                $this->writeInfoLog('User record has not existed in redis set. It found in DB and set to redis set', [
                    'telegram user id' => $telegramUserId,
                ]);
                $result = true;
            } else {
                $this->writeInfoLog('User record has not existed in redis set, nor in DB', [
                    'telegram user id' => $telegramUserId,
                ]);
                $result = false;
            }
        }
        $this->writeInfoLog('Result of the authorization by telegram user id', [
            'telegram user id' => $telegramUserId,
            'result' => $result,
        ]);
        return $result;
    }
}

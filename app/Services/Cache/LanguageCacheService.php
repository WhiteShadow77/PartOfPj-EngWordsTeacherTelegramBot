<?php

namespace App\Services\Cache;

use App\Models\User;
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\Cache;
use Predis\Client;

class LanguageCacheService
{
    use LoggerTrait;

    public function __construct()
    {
        $this->writeInfoLog('Created LanguageCacheService obj', [
            'obj id' => spl_object_id($this)
        ]);
    }

    private function getKeyByModel(User $userModel)
    {
        return $userModel->chat_id . '_user_language';
    }

    private function getKeyByChatId(string $chatId)
    {
        return $chatId . '_user_language';
    }

    public function getLanguageInsteadFromModel(User $userModel)
    {
        $key = $this->getKeyByModel($userModel);

        $language = Cache::get($key, function () use ($userModel, $key) {

            $language = $userModel->language;
            Cache::put($key, $language);

            $this->writeInfoLog('Got user language from user model and set to cache', [
                'user id' => $userModel->id,
                'key' => $key,
                'language' => $language
            ]);
            return $language;
        });

        $this->writeInfoLog('Got user language from cache', [
            'user id' => $userModel->id,
            'key' => $key,
            'language' => $language
        ]);

        return $language;
    }

    public function getLanguageInsteadFromDbByChatId(string $chatId)
    {
        $key = $this->getKeyByChatId($chatId);

        $language = Cache::get($key, function () use ($chatId, $key) {

            $userModel = User::where('chat_id', $chatId)->first();

            $language = $userModel->language;
            Cache::put($key, $language);

            $this->writeInfoLog('Got user language from DB by chat_id and set to cache', [
                'user id' => $userModel->id,
                'key' => $key,
                'language' => $language
            ]);

            return $language;
        });

        $this->writeInfoLog('Got user language from cache', [
            'chat id' => $chatId,
            'key' => $key,
            'language' => $language
        ]);

        return $language;
    }

    public function setNewLanguage(User $userModel, string $language)
    {
        $key = $this->getKeyByModel($userModel);
        Cache::put($key, $language);

        $this->writeInfoLog('Set new user language to cache', [
            'user id' => $userModel->id,
            'key' => $key,
            'language' => $language
        ]);
    }

    public function freeCache(string $chatId)
    {
        $key = $this->getKeyByChatId($chatId);
        Cache::forget($key);

        $this->writeInfoLog('Cache has made free', [
            'key' => $key,
        ]);
    }
}

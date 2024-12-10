<?php

namespace App\Services\Cache;

use Predis\Client;

class ErrorUserMessageCacheService
{
    private Client $redisClient;

    public function __construct()
    {
        $this->redisClient = new Client();
    }

    public function setErrorMessageInCache(string $chatId, string $text)
    {
        $this->redisClient->set(
            $chatId . '__error_message',
            $text,
            'EX',
            3600 * config('logging.error_message_ttl_in_cache_in_hours')
        );
    }

    public function getErrorMessageFromCache(string $chatId)
    {
        return $this->redisClient->get($chatId . '__error_message');
    }
}

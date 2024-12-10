<?php

namespace App\Services\Log;

use Predis\Client;

/** Sets or gets config for the logging */

class LogConfigService
{
    private Client $redisClient;

    public function __construct()
    {
        $this->redisClient = new Client();
    }

    public function setIsEnabledLogWrite(int $newValue)
    {
        $this->redisClient->set('is_enabled_log_write', $newValue);
    }

    public function getIsEnabledLogWrite()
    {
        return $this->redisClient->get('is_enabled_log_write');
    }

    public function setIsEnabledLogSend(int $newValue)
    {
        $this->redisClient->set('is_enabled_log_send', $newValue);
    }

    public function getIsEnabledLogSend()
    {
        return $this->redisClient->get('is_enabled_log_send');
    }

    public function getObjId()
    {
        return spl_object_id($this);
    }
}

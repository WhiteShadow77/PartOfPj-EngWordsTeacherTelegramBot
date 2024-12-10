<?php

namespace App\Services\Cache;

use App\Traits\LoggerTrait;
use Predis\Client;

class QuizCacheService
{
    use LoggerTrait;

    private Client $redisClient;

    public function __construct()
    {
        $this->redisClient = new Client();
    }

    public function setWrongAnsweredQuestionRightWordId(string|int $cacheFieldId, string|int $rightWordId)
    {
        $this->redisClient->sAdd($cacheFieldId, $rightWordId);

        $this->writeInfoLog('Successfully set wrong answered word id, which in question, to redis cache', [
            'cache filed id' =>  $cacheFieldId,
            'word id' => $rightWordId,

        ]);
    }

    public function isWorngAnswerWordIdExist(string|int $cacheFieldId, int $wordId)
    {
        $result = $this->redisClient->sismember($cacheFieldId, $wordId);

        $this->writeInfoLog('Got wrong answered words ids', [
            'cache filed id' =>  $cacheFieldId,
            'word id' => $wordId,
            'result' => $result,

        ]);

        return $result;
    }

    public function getWrongAnsweredWordsIds(string|int $cacheFieldId)
    {
        $wordsIds = $this->redisClient->sMembers($cacheFieldId);

        $this->writeInfoLog('Got wrong answered words ids', [
            'cache filed id' =>  $cacheFieldId,
            'words ids' => $wordsIds,
        ]);

        return $wordsIds;
    }

    public function freeWrongAnswerWordsIdsMemeory(string|int $cacheFieldId)
    {
        $wordsIds = $this->redisClient->del($cacheFieldId);

        $this->writeInfoLog('Has made free the memory of wrong answered words ids', [
            'cache filed id' =>  $cacheFieldId,

        ]);

        return $wordsIds;
    }
}

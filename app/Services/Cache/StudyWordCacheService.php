<?php

namespace App\Services\Cache;

use App\Traits\LoggerTrait;
use Predis\Client;

class StudyWordCacheService
{
    use LoggerTrait;

    private Client $redisClient;

    public function __construct()
    {
        $this->redisClient = new Client();
    }

    public function setStudyWordIdInCache(string $cacheIdentifier, int $studyWordId)
    {
        $this->redisClient->sAdd($cacheIdentifier, $studyWordId);
        $this->writeInfoLog('Has set study word id in cache', [
            'cache identifier' => $cacheIdentifier,
            'study word id' => $studyWordId,
        ]);
    }

    public function setMultipleStudyWordIdInCache(string $cacheIdentifier, array $studyWordsIds)
    {
        if (sizeof($studyWordsIds) > 0) {
            $this->redisClient->sAdd($cacheIdentifier, $studyWordsIds);
            $this->writeInfoLog('Has set multiple study words id in cache', [
                'cache identifier' => $cacheIdentifier,
                'study words ids' => $studyWordsIds,
            ]);
        } else {
            $this->writeErrorLog('Has not set in cache study words id. Nothing to add', [
                'cache identifier' => $cacheIdentifier,
                'study words ids' => $studyWordsIds,
            ]);
        }
    }

    public function getStudyWordsIdsFromCacheAndFree(string $cacheIdentifier): array
    {
        $result = $this->redisClient->sMembers($cacheIdentifier);
        $this->writeInfoLog('Got study words ids from cache and free cache', [
            'cache identifier' => $cacheIdentifier,
            'study words ids' => $result,
        ]);
        $this->redisClient->del($cacheIdentifier);
        return $result;
    }

    public function removeStudyWordIdFromCache(string $cacheIdentifier, int $studyWordId)
    {
        $this->redisClient->sRem($cacheIdentifier, $studyWordId);
        $this->writeInfoLog('Removed study word id from cache', [
            'cache identifier' => $cacheIdentifier,
            'study word id' => $studyWordId,
        ]);
    }
}

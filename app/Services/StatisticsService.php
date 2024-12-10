<?php

namespace App\Services;

use App\Models\User;
use App\Services\DataStructures\Statistcs\KnownWordsStatistics;
use App\Services\DataStructures\Statistcs\UnknownWordsStatistics;
use App\Traits\LoggerTrait;
use App\Models\Statistics;
use App\Enums\WordStatus;

class StatisticsService
{
    use LoggerTrait;

    private Statistics $statisticModel;

    public function __construct()
    {
        $this->statisticsModel = new Statistics();
    }

    public function addRecord(int $userId, WordStatus $wordStatus, ?int $englishWordId = null)
    {
        $statisticsModel = $this->statisticsModel->create([
            'user_id' => $userId,
            'word_status' => $wordStatus->name,
            'english_word_id' => $englishWordId,
            'StatisticsService obj id' => spl_object_id($this)
        ]);

        $this->writeInfoLog('Inserted record into statistics table ', [
            'user id' => $userId,
            'english_word_id' => $englishWordId,
            'word status' => $wordStatus->name,
            'StatisticsService obj id' => spl_object_id($this)
        ]);
        return $statisticsModel;
    }


    public function getWordsCountPerDay(int $userId, WordStatus $wordStatus, int $datesCountInPage = 30)
    {
        $result = $this->statisticsModel
            ->selectRaw('created_at as date, count(created_at) as ' . $wordStatus->name . '_words_count')
            ->where('word_status', $wordStatus->name)
            ->groupBy('created_at')->get()->toArray();

        array_splice($result, 0, -$datesCountInPage);

        $data = [];

        foreach ($result as $resultItem) {
            $date = explode(' ', $resultItem['date']);
            unset($date[1]);
            $data[$date[0]] = $resultItem[$wordStatus->name . '_words_count'];
        }

        $this->writeInfoLog('Got ' . $wordStatus->name . ' words count per day', [
            'user id' => $userId,
            'dates count in page' => $datesCountInPage,
            'data' => $data,
            'StatisticsService obj id' => spl_object_id($this)
        ]);

        if ($wordStatus->name == 'known') {
            $knownWordsStatistics = new KnownWordsStatistics();
            $knownWordsStatistics->setDatesAndWordsCounts($data);
            return $knownWordsStatistics;
        } else {
            $unknownWordsStatistics = new UnknownWordsStatistics();
            $unknownWordsStatistics->setDatesAndWordsCounts($data);
            return $unknownWordsStatistics;
        }
    }

    public function removeKnownWordsRecords(User $userModel, array $knownWordsIds)
    {
        $this->statisticsModel->whereIn('english_word_id', $knownWordsIds)->where('user_id', $userModel->id)->delete();

        $this->writeInfoLog(
            'Removed known words records from statistics table, using english_words_id foreign keys',
            [
                'user id' => $userModel->id,
                'english_words_id foreign keys' => $knownWordsIds
            ]
        );
    }
}

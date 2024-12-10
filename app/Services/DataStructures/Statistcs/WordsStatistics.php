<?php

namespace App\Services\DataStructures\Statistcs;

class WordsStatistics
{
    public array $datesAsKeyWordsCountsAsValues;
    protected ?array $dates = null;
    protected ?array $knownWordsCounts = null;
    protected ?array $unknownWordsCounts = null;
    protected ?array $forDebug = null;


    public function setDatesAndWordsCounts(array $datesAsKeyWordsCountsAsValues)
    {
        $this->datesAsKeyWordsCountsAsValues = $datesAsKeyWordsCountsAsValues;
    }

    public function merge(KnownWordsStatistics $knownObj, UnknownWordsStatistics $unknownObj)
    {
        $knownWordsCounts = [];
        $unknownWordsCounts = [];

        foreach ($knownObj->datesAsKeyWordsCountsAsValues as $date => $wordsCounts) {
            $knownWordsCounts[$date] = $wordsCounts;
            if (!isset($unknownObj->datesAsKeyWordsCountsAsValues[$date])) {
                $unknownWordsCounts[$date] = 0;
            } else {
                $unknownWordsCounts[$date] = $unknownObj->datesAsKeyWordsCountsAsValues[$date];
            }
        }

        foreach ($unknownObj->datesAsKeyWordsCountsAsValues as $date => $wordsCounts) {
            $unknownWordsCounts[$date] = $wordsCounts;
            if (!isset($knownObj->datesAsKeyWordsCountsAsValues[$date])) {
                $knownWordsCounts[$date] = 0;
            } else {
                $knownWordsCounts[$date] = $knownObj->datesAsKeyWordsCountsAsValues[$date];
            }
        }

        ksort($knownWordsCounts);
        ksort($unknownWordsCounts);

        $this->dates = array_keys($knownWordsCounts);
        $this->knownWordsCounts = array_values($knownWordsCounts);
        $this->unknownWordsCounts = array_values($unknownWordsCounts);
        $this->forDebug = [
            'known words counts' =>  $knownWordsCounts,
            'unknown words counts' => $unknownWordsCounts
        ];
    }

    public function getDates()
    {
        return $this->dates;
    }

    public function getKnownWordsCounts()
    {
        return $this->knownWordsCounts;
    }

    public function getUnknownWordsCounts()
    {
        return $this->unknownWordsCounts;
    }

    public function getDebugInfo()
    {
        return $this->forDebug;
    }
}

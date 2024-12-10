<?php

namespace App\Services\Controller;

use App\Services\UserService;

class StatisticsControllerService
{
    public function getStatisticsTemplate(UserService $userService, int $userId)
    {
        $knownWordsCount = $userService->getDistinctKnownWordsCountById($userId);
        $allEnglishWordsCount = $userService->getAllWordsCount();

        $progressPercents = 100 * $knownWordsCount / $allEnglishWordsCount;
        $progressPercents = round($progressPercents, 4);

        return view('home.statistics', [
            'progressPercents' => $progressPercents,
            'knownWordsCount' => $knownWordsCount,
            'allWordsCount' => $allEnglishWordsCount
        ]);
    }
}

<?php

namespace App\Services\Controller;

use App\Models\User;
use App\Services\EnglishWordService;
use App\Services\UserService;

class HomeControllerService
{
    public function getHomeTemplate(UserService $userService, int $userId)
    {
        $knownWordsCount = $userService->getKnownWordsCountById($userId);

        return view('home.home', [
            'knownWordsCount' => $knownWordsCount,
            'allWordsCount' => $userService->getAllWordsCount()
        ]);
    }

    public function getAllKnownWords(int $userId, UserService $userService, EnglishWordService $englishWordService)
    {
        $knownWordsIds = $userService->getKnownWordsIds(User::find($userId));
        $words = $englishWordService->getPortionOfEnglishWordsByIdsCollection($knownWordsIds, 3);

        return view('home.words.known', ['words' => $words]);
    }
}

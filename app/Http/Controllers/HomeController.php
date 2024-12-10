<?php

namespace App\Http\Controllers;

use App\Enums\SentWordsKind;
use App\Models\User;
use App\Services\Controller\HomeControllerService;
use App\Services\EnglishWordService;
use App\Services\UserService;
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    use LoggerTrait;

    private HomeControllerService $homeControllerService;

    public function __construct(HomeControllerService $homeControllerService)
    {
        $this->homeControllerService = $homeControllerService;
    }

    public function getHomeTemplate(UserService $userService)
    {
        $userId = Auth::user()->id;
        return $this->homeControllerService->getHomeTemplate($userService, $userId);
    }

    public function getAllKnownWords(int $userId, UserService $userService, EnglishWordService $englishWordService)
    {
        return $this->homeControllerService->getAllKnownWords($userId, $userService, $englishWordService);
    }
}

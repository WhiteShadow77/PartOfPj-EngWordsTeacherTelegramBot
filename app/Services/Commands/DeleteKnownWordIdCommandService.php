<?php

namespace App\Services\Commands;

use App\Models\User;
use App\Services\UserService;

class DeleteKnownWordIdCommandService
{
    public function __invoke(UserService $userService): void
    {
        $userModel = User::find($this->argument('user_id'));
        $userService->deleteKnownWordById($userModel, $this->argument('id'));
        echo 'Known words ids: ';
        print_r($userService->getKnownWordsIds($userModel));
    }
}

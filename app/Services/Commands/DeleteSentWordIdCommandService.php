<?php

namespace App\Services\Commands;

use App\Models\User;
use App\Services\UserService;
use App\Traits\LoggerTrait;

class DeleteSentWordIdCommandService
{
    use LoggerTrait;

    public function __invoke(UserService $userService): void
    {
        $userModel = User::find($this->argument('user_id'));
        $userService->deleteSentWordId($userModel, $this->argument('id'));

        echo 'Send words ids: ';

        print_r($userService->getSentWordsIds($userModel, SentWordsKind::sent_words_id));
    }
}

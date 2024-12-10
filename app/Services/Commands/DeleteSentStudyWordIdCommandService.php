<?php

namespace App\Services\Commands;

use App\Models\User;
use App\Services\UserService;
use App\Traits\LoggerTrait;
use App\Enums\SentWordsKind;

class DeleteSentStudyWordIdCommandService
{
    use LoggerTrait;

    public function __invoke(UserService $userService): void
    {
        $userModel = User::find($this->argument('user_id'));
        $userService->deleteSentStudyWordId($userModel, $this->argument('id'));
        echo 'Sent study words ids: ';
        print_r($userService->getSentWordsIds($userModel, SentWordsKind::sent_study_words_id));
    }
}

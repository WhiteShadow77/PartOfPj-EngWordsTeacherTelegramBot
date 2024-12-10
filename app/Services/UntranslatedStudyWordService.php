<?php

namespace App\Services;

use App\Models\UntranslatedStudyWord;
use App\Models\User;
use App\Traits\LoggerTrait;

class UntranslatedStudyWordService
{
    use LoggerTrait;

    public function insertUntranslatedWordAndAttachToUser(User $userModel, string $word): void
    {
        $userId = $userModel->id;
        UntranslatedStudyWord::create([
            'word' => $word,
            'user_id' => $userId
        ]);
        $this->writeInfoLog('Inserted untranslated study word to table and attached to a user', [
            'word' => $word,
            'user_id' => $userId
        ]);
    }
}

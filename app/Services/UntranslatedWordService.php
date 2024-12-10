<?php

namespace App\Services;

use App\Models\UntranslatedWordId;
use App\Traits\LoggerTrait;

class UntranslatedWordService
{
    use LoggerTrait;

    public function insertUntranslatedWordWithWord(int $wordId, string $word): void
    {
        $this->writeInfoLog('Word has not been translated', [
            'word id' => $wordId,
            'word' => $word,
            'type' => 'English word'
        ]);
        $wordModel = UntranslatedWordId::create([
            'eng_word_id' => $wordId
        ]);
        $this->writeInfoLog('Untranslated word id has successfully inserted in DB', [
            'model' => $wordModel->toArray(),
            'word id' => $wordId,
            'word' => $word,
            'type' => 'English word'
        ]);
    }

    public function insertUntranslatedWord(int $wordId): void
    {
        $this->writeInfoLog('Word has not been translated by id', [
            'word id' => $wordId,
            'type' => 'English word'
        ]);
        $wordModel = UntranslatedWordId::create([
            'eng_word_id' => $wordId
        ]);
        $this->writeInfoLog('Untranslated word id has successfully inserted in DB', [
            'model' => $wordModel->toArray(),
            'word id' => $wordId,
            'type' => 'English word'
        ]);
    }
}

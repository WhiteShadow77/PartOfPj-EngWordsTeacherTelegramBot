<?php

namespace App\Services;

use App\Models\EnglishWord;
use App\Models\RussianWord;
use App\Traits\LoggerTrait;

class RussianWordService
{
    use LoggerTrait;

    public function assignWords(string $englishWord, array $russianWords)
    {
        $this->writeInfoLog('Assigning english word with russian in DB', [
            'english word' => $englishWord,
            'russian words' => $russianWords
        ]);

        $englishWordModel = EnglishWord::where('word', '=', $englishWord)->first();
        $this->writeInfoLog('Got englishWordModel', [
            'model' => $englishWordModel
        ]);
        foreach ($russianWords as $russianWord) {
            $russianWordModel = RussianWord::create([
               'word' => $russianWord
            ]);
            $this->writeInfoLog('Created russianWord model', [
                'model' => $russianWordModel
            ]);
            $russianWordModel->englishWords()->attach($englishWordModel->id);
            $this->writeInfoLog('Attached russian word with english word', [
                'russian word id' => $russianWordModel->id,
                'english word id' => $englishWordModel->id
            ]);
        }
        return true;
    }

    public function assignWordsByEnglishWordModel(EnglishWord $englishWordModel, array $russianWords)
    {
        $this->writeInfoLog('Assigning english word with russian in DB, by english word model', [
            'english word id' => $englishWordModel->id,
            'english word' => $englishWordModel->word,
            'russian words' => $russianWords
        ]);

        foreach ($russianWords as $russianWord) {
            $russianWordModel = RussianWord::create([
                'word' => $russianWord
            ]);
            $this->writeInfoLog('Created russianWord model', [
                'model' => $russianWordModel
            ]);
            $russianWordModel->englishWords()->attach($englishWordModel->id);
            $this->writeInfoLog('Attached russian word with english word', [
                'russian word id' => $russianWordModel->id,
                'english word id' => $englishWordModel->id
            ]);
        }
        return true;
    }
}

<?php

namespace App\Services\Commands;

use App\Models\EnglishWord;
use App\Traits\LoggerTrait;
use Illuminate\Console\OutputStyle;

class DBFullerCommandService
{
    use LoggerTrait;

    public function __invoke(OutputStyle $outputStyle): void
    {
        $this->writeInfoLog('\'Filling english words to DB\' command executing');

        $words = file(app_path() . '/../' . config('english_words.english_words_dictionary_path'));

        $bar = $outputStyle->createProgressBar(count($words));
        $this->fillDB($words, $bar);
        echo PHP_EOL;
    }

    private function fillDB(array $englishWords, &$bar)
    {
        $bar->start();
        foreach ($englishWords as $word) {
            $englishWord = new EnglishWord();
            $englishWord->word = trim($word);
            $englishWord->save();
            unset($englishWord);
            $bar->advance();
        }
        $bar->finish();
    }
}

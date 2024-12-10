<?php

namespace App\Services\Commands;

use App\Models\EnglishWord;
use App\Models\UntranslatedWordId;
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\DB;

class DeleteUntranslatedCommandService
{
    use LoggerTrait;

    public function __invoke(): void
    {
        $beforeCount = EnglishWord::orderBy('id', 'asc')->count();
        echo 'Before ', $beforeCount, PHP_EOL;
        $notTranslatedWords = UntranslatedWordId::all();
        $result = [];
        $notTranslatedWords->each(function ($item) use (&$result) {
            $result[$item->eng_word_id] = $item->eng_word_id;
        });

        DB::table('english_words')
            ->whereIn('id', $result)
            ->delete();

        DB::table('untranslated_word_ids')->delete();

        $afterCount = EnglishWord::orderBy('id', 'asc')->count();
        $deleted = $beforeCount - $afterCount;
        echo 'After ', $afterCount, PHP_EOL;
        echo 'Deleted ' , $deleted, PHP_EOL;

        $this->writeInfoLog(
            '\'Delete untranslated words\' command executed',
            [
            'Deleted' => $deleted
            ],
            isAllowedSendToTlg: true
        );
    }
}

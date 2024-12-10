<?php

namespace App\Services;

use App\Models\EnglishWord;
use App\Models\StudyWord;
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\DB;

class StudyWordsService
{
    use LoggerTrait;

    /** Gets english words by ids returns result order in incoming order of ids.
     *
     * @param array $ids
     * @return array
     */
    public function getWordsByIds(array $ids): array
    {
        $result = [];
        $buffer = EnglishWord::whereIn('id', $ids)->get()->pluck('word', 'id')->toArray();

        foreach ($ids as $id) {
            $result[$id] = $buffer[$id];
        }

        $this->writeInfoLog('Got couple of study words by ids', [
            'ids' => $ids,
            'words' => $result
        ]);
        return $result;
    }

    /** Returns array of russians translates according to study words ids which are keys of result array.
     *
     * @param array $ids
     * @return array|null
     */
    public function translateSeveralByStudyWordIds(array $ids): ?array
    {
        if (sizeof($ids) > 0) {
            $result = [];
            $buffer = DB::table('english_words')
                ->join(
                    'english_word_russian_word',
                    'english_word_russian_word.english_word_id',
                    '=',
                    'english_words.id'
                )
                ->join(
                    'russian_words',
                    'english_word_russian_word.russian_word_id',
                    '=',
                    'russian_words.id'
                )
                ->whereIn('english_words.id', $ids)
                ->selectRaw('english_words.id as study_word_id')
                ->selectRaw('russian_words.word as rus_word')
                ->get()->groupBy('study_word_id')->toArray();

            foreach ($buffer as &$item) {
                foreach ($item as &$value) {
                    $value = $value->rus_word;
                }
            }

            foreach ($ids as $id) {
                $result[$id] = $buffer[$id];
            }

            $this->writeInfoLog('Got several russian translates of study words', [
                'result' => $result,
                'study words ids' => $ids,
            ]);
            return $result;
        } else {
            $this->writeInfoLog('Several russian translates result of study words is empty. Null returned', [
                'study words ids' => $ids,
            ]);
            return null;
        }
    }

    /** Writes english_words_id in study words table and returns true. If english word not found returns false.
     *
     * @param string $englishWord
     * @param int $studyWordId
     * @return bool
     */
    public function assignWithEnglishWord(string $englishWord, int $studyWordId): bool
    {
        $this->writeInfoLog('Assigning english word with study word', [
            'english word' => $englishWord,
            'study word id' => $studyWordId
        ]);
        $englishWordModel = EnglishWord::where('word', $englishWord)->first();
        if (!is_null($englishWordModel)) {
            StudyWord::where('id', $studyWordId)->update([
                'english_words_id' => $englishWordModel->id
            ]);
            $this->writeInfoLog('Successfully assigned english word with study word', [
                'english word model' => $englishWordModel,
                'study word id' => $studyWordId
            ]);
            return true;
        } else {
            $this->writeErrorLog('English word not found in english_words table', [
                'english word' => $englishWord
            ]);
            return false;
        }
    }
}

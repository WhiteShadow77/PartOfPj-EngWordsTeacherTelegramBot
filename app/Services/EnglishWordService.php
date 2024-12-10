<?php

namespace App\Services;

use App\Enums\SentWordsKind;
use App\Models\EnglishWord;
use App\Models\RussianWord;
use App\Traits\LoggerTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EnglishWordService
{
    use LoggerTrait;

    public function deleteById($id)
    {
        EnglishWord::where('id', $id)->delete();
    }

    public function getWordsCount()
    {
        return (EnglishWord::orderBy('id', 'asc'))->count();
    }

    /** Translates english word to russian. Returns null if translate not found in DB.
     * Returns false if english word found in DB, but not found translate.
     *
     * @param string $englishWord
     * @return array|bool|null
     */
    public function translate(string $englishWord): array|bool|null
    {
        $this->writeInfoLog('Looking for a translate in DB', [
            'english word to translate' => $englishWord
        ]);

        if ($englishWord == '') {
            $this->writeErrorLog('Translate of empty field is impossible', [
                'english word to translate' => $englishWord
            ]);
            throw new \RuntimeException('Translate of empty field is impossible');
        } else {
            $englishWord = EnglishWord::where('word', $englishWord)->first();

            if (!is_null($englishWord)) {
                $translate = $englishWord->russianWords;
                $this->writeInfoLog('English word found in DB', [
                    'id' => $englishWord->id,
                    'english word to translate' => $englishWord->word,
                ]);

                if (sizeof($translate) > 0) {
                    $translate = $translate->pluck('word')->toArray();
                    $this->writeInfoLog('Translate from DB done', [
                        'translate' => $translate,
                    ]);
                    return $translate;
                } else {
                    $this->writeInfoLog('Translate from DB failed', [
                        'english word' => $englishWord,
                    ]);
                    return null;
                }
            } else {
                $this->writeErrorLog('English word not found in DB', [
                    'study word' => $englishWord
                ]);
                return false;
            }
        }
    }

    /** Adds new english word in DB.
     *
     * @param string $word
     * @return void
     */
    public function addNewWord(string $word): void
    {
        $newEnglishWordModel = EnglishWord::create([
            'word' => $word
        ]);
        $this->writeErrorLog('New english word was added to DB', [
            'english word model' => $newEnglishWordModel
        ]);
    }

    /** Translates several english words from array of ids of words to russian words.
     *
     * @param array $englishWordsIds
     * @return array|null
     */
    public function translateSeveral(array $englishWordsIds): ?array
    {
        if (sizeof($englishWordsIds) > 0) {
            $result = DB::table('english_words')
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
                ->whereIn('english_words.id', $englishWordsIds)
                ->selectRaw('english_words.id as english_word_id')
                ->selectRaw('russian_words.word as rus_word')
                ->get()->groupBy('english_word_id')->toArray();

            foreach ($result as &$item) {
                foreach ($item as &$value) {
                    $value = $value->rus_word;
                }
            }
            $this->writeInfoLog('Got several russian translates of english words', [
                'result' => $result,
                'study words ids' => $englishWordsIds,
            ]);
            return $result;
        } else {
            $this->writeInfoLog('Several russian translates result of english words is empty. Null returned', [
                'study words ids' => $englishWordsIds,
            ]);
            return null;
        }
    }

    /** Checks if english word is exist in table then returns model, instead return false.
     *
     * @param string $englishWord
     * @return EnglishWord|bool
     */
    public function getEnglishWordModelIfExist(string $englishWord): EnglishWord|bool
    {
        $englishWordModel = EnglishWord::where('word', $englishWord)->first();
        if (!is_null($englishWordModel)) {
            $result = $englishWordModel;
        } else {
            $result = false;
        }
        $this->writeInfoLog('Got englishWordModel if exists', [
            'english word' => $englishWord,
            'result' => $result
        ]);
        return $result;
    }

    /** Returns english word model if exists in table, instead return null.
     *
     * @param string $englishWord
     * @return EnglishWord|null
     */
    public function getEnglishWordModel(string $englishWord): ?EnglishWord
    {
        $englishWordModel = EnglishWord::where('word', $englishWord)->first();
        $this->writeInfoLog('Got englishWordModel', [
            'english word' => $englishWord,
            'result' => $englishWordModel
        ]);
        return $englishWordModel;
    }

    /** Returns an array with id as key, word as value.
     *
     * @param array $ids
     * @return array
     */
    public function getSeveralEnglishWordsByIds(array $ids): array
    {
        return EnglishWord::whereIn('id', $ids)->get()->pluck('word', 'id')->toArray();
    }

    /** Returns portion of the collection of english words by ids.
     *
     * @param array $ids
     * @param int $portion
     * @return mixed
     */
    public function getPortionOfEnglishWordsByIdsCollection(array $ids, int $portion)
    {
        return EnglishWord::whereIn('id', $ids)->paginate($portion);
    }

    /** Returns the collection of english words by ids.
     *
     * @param array $ids
     * @return \ArrayIterator|\Traversable
     */
    public function getCollectionOfEnglishWordsByIds(array $ids): \ArrayIterator|\Traversable
    {
        $result = collect();
        $englishWords = EnglishWord::whereIn('id', $ids)->get();
        foreach ($ids as $id) {
            foreach ($englishWords as $englishWord) {
                if ($id == $englishWord->id) {
                    $result->add($englishWord);
                }
            }
        }
        $result = $result->getIterator();
        $this->writeInfoLog('Got the collection of english words', [
            'ids array' => $ids,
            'english word collection' => $result,
        ]);
        return $result;
    }

    /** Trying to translate english word from DB. If english word not found in DB returns false.
     * If english word found but not found the translation then returns null. If the translation found returns the array
     * of translate.
     *
     * @param int $englishWordId
     * @param EnglishWord|null $englishWord
     * @return array|bool|null
     */
    public function translateById(int $englishWordId, ?EnglishWord &$englishWord = null): array|bool|null
    {
        $this->writeInfoLog('Looking for a translate in DB by english word id', [
            'english word id' => $englishWordId
        ]);

        $englishWordModel = EnglishWord::find($englishWordId);

        if (!is_null($englishWordModel)) {
            $translate = $englishWordModel->russianWords;
            $this->writeInfoLog('English word found in DB', [
                'id' => $englishWordModel->id,
                'english word to translate' => $englishWordModel->word,
            ]);

            if (sizeof($translate) > 0) {
                $translate = $translate->pluck('word')->toArray();
                $this->writeInfoLog('Translate from DB done', [
                    'translate' => $translate,
                    'type' => 'english word'
                ], isAllowedSendToTlg: true);
                $englishWord = $englishWordModel;
                return $translate;
            } else {
                $this->writeInfoLog('Translate from DB failed', [
                    'english word' => $englishWordModel,
                ]);
                $englishWord = $englishWordModel;
                return null;
            }
        } else {
            $this->writeErrorLog('English word not found in DB', [
                'study word' => $englishWordModel
            ]);
            return false;
        }
    }

    /** Returns the quantity of translated english words.
     *
     * @return int
     */
    public function getTranslatedWordsCount(): int
    {
        $result = DB::table('english_word_russian_word')
            ->selectRaw('count(distinct english_word_id) as count')
            ->pluck('count')->toArray();
        $result = current($result);

        $this->writeInfoLog('Got the quantity of translated english words', [
            'result' => $result
        ]);
        return $result;
    }

    /** Inserts english word, russian translates and attaches them.
     *
     * @param string $englishWord
     * @param array $russianTranslates
     */
    public function addEnglishWordAddRussianTranslatesAndAttach(string $englishWord, array $russianTranslates): void
    {
        $this->writeInfoLog('Adding new english word, adding new russian translates and attaching', [
            'english word' => $englishWord,
            'russian translates' => $russianTranslates
        ]);
        $englishWordModel = EnglishWord::create([
            'word' => $englishWord
        ]);
        foreach ($russianTranslates as $russianTranslate) {
            $russianWordModel = RussianWord::create([
                'word' => $russianTranslate
            ]);
            $this->writeInfoLog('Added new russian word', [
                'model' => $russianWordModel
            ]);
            $russianWordModel->englishWords()->attach($englishWordModel->id);
            $this->writeInfoLog('Attached russian word with english word');
        }
    }

    /** Adds transcription and  dirs with file names the english word in DB.
     *
     * @param EnglishWord $englishWordModel
     * @param string $transcription
     * @param string $ukPronFileNamesAndDirs
     * @param string $usPronFileNamesAndDirs
     */
    public function addTranscriptionAndPronFileNamesAndDirs(
        EnglishWord $englishWordModel,
        string $transcription,
        string $ukPronFileNamesAndDirs,
        string $usPronFileNamesAndDirs
    ): void {
        $englishWordModel->update([
            'transcription' => $transcription,
            'uk_pron_file' => $ukPronFileNamesAndDirs,
            'us_pron_file' => $usPronFileNamesAndDirs
        ]);
        $this->writeInfoLog('Added transcription and pron. file names and dirs to english word', [
            'word' => $englishWordModel->word,
            'transcription' => $transcription,
            'for uk pron.' => $ukPronFileNamesAndDirs,
            'for us pron.' => $usPronFileNamesAndDirs,
        ]);
    }

    /** Adds english word, transcription, pron. file names and dirs to the table english_words.
     *
     * @param string $englishWord
     * @param string $transcription
     * @param string $ukPronFileNamesAndDirs
     * @param string $usPronFileNamesAndDirs
     * @return EnglishWord
     */
    public function addEnglishWordTranscriptionPronFileNamesAndDirs(
        string $englishWord,
        string $transcription,
        string $ukPronFileNamesAndDirs,
        string $usPronFileNamesAndDirs
    ): EnglishWord {
        $englishWordModel = EnglishWord::create([
            'word' => $englishWord,
            'transcription' => $transcription,
            'uk_pron_file' => $ukPronFileNamesAndDirs,
            'us_pron_file' => $usPronFileNamesAndDirs
        ]);

        $this->writeInfoLog('Added english word, transcription and pron. file names and dirs to english_words table', [
            'word' => $englishWord,
            'transcription' => $transcription,
            'for uk pron.' => $ukPronFileNamesAndDirs,
            'for us pron.' => $usPronFileNamesAndDirs,
        ]);
        return $englishWordModel;
    }

    /** Translates from several english words.
     *
     * @param array $englishWords
     * @return array|null
     */
    public function translateSeveralByWords(array $englishWords): ?array
    {
        if (sizeof($englishWords) > 0) {
            $result = DB::table('english_words')
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
                ->whereIn('english_words.word', $englishWords)
                ->selectRaw('english_words.id as english_word_id')
                ->selectRaw('russian_words.word as rus_word')
                ->get()->groupBy('english_word_id')->toArray();


            foreach ($result as &$item) {
                foreach ($item as &$value) {
                    $value = $value->rus_word;
                }
            }
            $this->writeInfoLog('Got several russian translates of english words', [
                'result' => $result,
                'words' => $englishWords,
            ]);
            return $result;
        } else {
            $this->writeInfoLog('Several russian translates result of english words is empty. Null returned', [
                'words' => $englishWords,
            ]);
            return null;
        }
    }

    /** Gets english words by array of ids.
     *
     * @param array $ids
     * @return array
     */
    public function getWordsByIds(array $ids): array
    {
        $result = EnglishWord::whereIn('id', $ids)->get()->pluck('word')->toArray();

        $this->writeInfoLog('Got english words by ids', [
            'ids' => $ids,
            'words' => $result
        ]);
        return $result;
    }

    /** Gets collection of english words models.
     *
     * @param array $knownWordsIds
     * @param array|null $englishWords
     * @return mixed
     */
    public function getEnglishWordsModelsCollection(array $knownWordsIds, ?array &$englishWords = null): mixed
    {
        $englishWordsModels = EnglishWord::find($knownWordsIds);
        $result = $englishWordsModels->getIterator();

        $englishWords = $englishWordsModels->pluck('word')->toArray();
        return $result;
    }

    /** Checks if english word model has translations.
     *
     * @param EnglishWord $englishWordModel
     * @return bool
     */
    public function hasEnglishWordModelTranslations(EnglishWord $englishWordModel)
    {
        return $englishWordModel->russianWords()->count() > 0;
    }
}

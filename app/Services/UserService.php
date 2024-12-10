<?php

namespace App\Services;

use App\Enums\SentWordsKind;
use App\Enums\WordKind;
use App\Enums\AnswerKind;
use App\Enums\WordStatus;
use App\Models\EnglishWord;
use App\Models\User;
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Services\History\HistoryRecordService;

class UserService
{
    use LoggerTrait;

    private EnglishWordService $englishWordService;


    public function __construct(EnglishWordService $englishWordService)
    {
        $this->englishWordService = $englishWordService;
    }

    /** Gets quantity of english words for a User.
     *
     * @return int
     */
    public function getCurrentEnglishWordsQuantity(): int
    {
        return Auth::user()->eng_words_per_twitch;
    }

    /** Gets quantity of english words for a User by chat id.
     *
     * @param string $chatId
     * @return int
     */
    public function getCurrentEnglishWordsQuantityByChatId(string $chatId)
    {
        return User::where('chat_id', $chatId)->first()->eng_words_per_twitch;
    }

    /** Gets minimal available quantity of english words for a User.
     *
     * @return int
     */
    public function getEnglishWordsMinAvailableQuantity()
    {
        return config('english_words.portion.min');
    }

    /** Gets maximal available quantity of english words for a User.
     *
     * @return int
     */
    public function getEnglishWordsMaxAvailableQuantity()
    {
        return config('english_words.portion.max');
    }

    /** Sets quantity of englishWords for a User by chat id.
     *
     * @param string $chatId
     * @param int $quantity
     */
    public function setCurrentEnglishWordsPortionByChatId(string $chatId, int $quantity): void
    {
        User::where('chat_id', $chatId)->update([
            'eng_words_per_twitch' => $quantity
        ]);
    }

    /** Gets user's current quantity of answers in quiz.
     *
     * @return int
     */
    public function getCurrentQuizAnswersQuantity(): int
    {
        return Auth::user()->quiz_max_answers;
    }

    /** Gets available quantity if quizzes.
     *
     * @return int
     */
    public function getQuizAvailableQuantity(): int
    {
        return config('quiz.available_quantity');
    }

    /** Gets user's current language.
     *
     * @return string
     */
    public function getCurrentLanguage(): string
    {
        return Auth::user()->language;
    }

    /** Gets user's is_enabled config by chat id.
     *
     * @param string $chatId
     * @return bool
     */
    public function getQuizIsEnabledByChatId(string $chatId): bool
    {
        return (bool)User::where('chat_id', $chatId)->first()->is_enabled_quiz_sending;
    }

    /** Gets user's twitch (english words distribution) is_enabled config by chat id.
     *
     * @param string $chatId
     * @return bool
     */
    public function getTwitchIsEnabledByChatId(string $chatId): bool
    {
        return (bool)User::where('chat_id', $chatId)->first()->is_enabled_english_words_sending;
    }

    /** Sets user;s is_enabled quiz config by chat_id.
     *
     * @param string $chatId
     * @param bool $isEnable
     */
    public function setQuizIsEnabledByChatId(string $chatId, bool $isEnable): void
    {
        User::where('chat_id', $chatId)->update(['is_enabled_quiz_sending' => $isEnable]);
    }

    /** Sets user's twitch (english words distribution) is_enabled config by chat id.
     *
     * @param string $chatId
     * @param bool $isEnable
     */
    public function setTwitchIsEnabledByChatId(string $chatId, bool $isEnable): void
    {
        User::where('chat_id', $chatId)->update(['is_enabled_english_words_sending' => $isEnable]);
    }

    /** Attaches sent word, to user, using sent_words table.
     *
     * @param User $userModel
     * @param $wordId
     * @return bool
     */
    public function addSentWordId(User $userModel, $wordId): bool
    {
        $this->writeInfoLog('addSentWordId method executing');

        $userModel->sentWords()->syncWithoutDetaching($wordId);
        $this->writeInfoLog('Successfully added sent word to user', [
            'user' => [
                'id' => $userModel->id,
                'email' => $userModel->email,
            ],
            'word id' => $wordId,
        ]);
        return true;
    }

    /** Get ids of words that has been already sent to a user using sent_words table.
     *
     * @param User $userModel
     * @return array
     */
    public function getSentWordsIds(User $userModel): array
    {
        $result = $userModel->sentWords->pluck('id')->toArray();

        $this->writeInfoLog('Got sent words ids of user using sent_words table', [
            'user id' => $userModel->id,
            'ids' => $result
        ]);
        return $result;
    }

    /** Adds study word, to user, using study_words table.
     *
     * @param User $userModel
     * @param $wordId
     * @return bool
     */
    public function addStudyWordId(User $userModel, $wordId): bool
    {
        $this->writeInfoLog('addStudyWordId method executing');

        $userModel->studyWords()->syncWithoutDetaching($wordId);
        $this->writeInfoLog('Successfully added study word to user', [
            'user' => [
                'id' => $userModel->id,
                'email' => $userModel->email,
            ],
            'word id' => $wordId,
        ]);
        return true;
    }

    /** Get ids of study words using study_words table.
     *
     * @param User $userModel
     * @return array
     */
    public function getStudyWordsIds(User $userModel): array
    {
        $result = $userModel->studyWords->pluck('id')->toArray();

        $this->writeInfoLog('Got study words ids of user using study_words table', [
            'user id' => $userModel->id,
            'ids' => $result
        ]);
        return $result;
    }


    /** Adds sent study word, to user, using sent_study_words table.
     *
     * @param User $userModel
     * @param $wordId
     * @return bool
     */
    public function addSentStudyWordId(User $userModel, $wordId): bool
    {
        $this->writeInfoLog('addSentStudyWordId method executing');
        $userModel->sentStudyWords()->syncWithoutDetaching($wordId);
        $this->writeInfoLog('Successfully added sent study word to user', [
            'user' => [
                'id' => $userModel->id,
                'email' => $userModel->email,
            ],
            'word id' => $wordId
        ]);
        return true;
    }

    /** Get ids of sent study words using ssent_study_words table.
     *
     * @param User $userModel
     * @return array
     */
    public function getSentStudyWordsIds(User $userModel): array
    {
        $result = $userModel->sentStudyWords->pluck('id')->toArray();

        $this->writeInfoLog('Got sent study words ids of user using sent_study_words table', [
            'user id' => $userModel->id,
            'ids' => $result
        ]);
        return $result;
    }

    /** Adds untranslated study word, to user, using untranslated_study_words_m table.
     *
     * @param User $userModel
     * @param $wordId
     * @return bool
     */
    public function addUntranslatedStudyWordId(User $userModel, $wordId): bool
    {
        $this->writeInfoLog('adduntranslatedStudyWordId method executing');

        $userModel->untranslatedStudyWords()->syncWithoutDetaching($wordId);
        $this->writeInfoLog('Successfully added untranslated study word to user', [
            'user' => [
                'id' => $userModel->id,
                'email' => $userModel->email,
            ],
            'word id' => $wordId,
        ]);
        return true;
    }

    /** Get ids of untranslated study words using untranslated_study_words_m table.
     *
     * @param User $userModel
     * @return array
     */
    public function getUntranslatedStudyWordsIds(User $userModel): array
    {
        $result = $userModel->untranslatedStudyWords->pluck('id')->toArray();

        $this->writeInfoLog('Got untranslated study words ids of user using untranslated_study_words_m table', [
            'user id' => $userModel->id,
            'ids' => $result
        ]);
        return $result;
    }

    /** Adds known word, to user, using known_words table.
     *
     * @param User $userModel
     * @param $wordId
     * @return bool
     */
    public function addknownWordId(User $userModel, $wordId): bool
    {
        $this->writeInfoLog('addknownWordId method executing');

        $userModel->knownWords()->sync($wordId);
        $this->writeInfoLog('Successfully added known word to user', [
            'user' => [
                'id' => $userModel->id,
                'email' => $userModel->email,
            ],
            'word id' => $wordId,
        ]);
        return true;
    }

    /** Get ids of known words using known_words table.
     *
     * @param User $userModel
     * @return array
     */
    public function getKnownWordsIds(User $userModel): array
    {
        $result = $userModel->knownWords->pluck('id')->toArray();

        $this->writeInfoLog('Got known words ids of user using known_words table', [
            'user id' => $userModel->id,
            'ids' => $result
        ]);
        return $result;
    }

    /** Get the count of user's known words by user id, using known_words table.
     *
     * @param int $idUser
     * @return int
     */
    public function getKnownWordsCountById(int $idUser): int
    {
        $userModel = User::find($idUser);
        $result = $userModel->knownWords()->count();
        $this->writeInfoLog('Successfully taken count of known words of user', [
            'user' => [
                'id' => $userModel->id,
                'email' => $userModel->email,
            ],
            'result' => $result
        ]);
        return $result;
    }

    /** Get distinct count of user's known words by user id, using knopwn_words table.
     *
     * @param int $idUser
     * @return int
     */
    public function getDistinctKnownWordsCountById(int $idUser): int
    {
        $userModel = User::find($idUser);
        $result = $userModel->knownWords()->distinct('english_word_id')->count();
        $this->writeInfoLog('Successfully taken distinct count of known words of user', [
            'user' => [
                'id' => $userModel->id,
                'email' => $userModel->email,
            ],
            'result' => $result
        ]);
        return $result;
    }

    /** Gets all english words quantity.
     *
     * @return int
     */
    public function getAllWordsCount(): int
    {
        return $this->englishWordService->getWordsCount();
    }

    /** Adds sutdy word to user if found in DB. If word not found in english_words table creates new and adds to user.
     * Returns word id of study word, false if uer not found.
     *
     * @param User $userModel
     * @param string $studyWord
     * @return int|bool
     */
    public function addOrCreateStudyWordAndAddToUser(User $userModel, string $studyWord): int|bool
    {
        $this->writeInfoLog('Adding study word id to the user or create in english words table and add to user', [
            'study_word' => $studyWord,
        ]);
        if ($studyWord == '') {
            $this->writeInfoLog('Empty study word');
            return false;
        }

        $englishWordModel = EnglishWord::where('word', $studyWord)->first();
        if (!is_null($englishWordModel)) {
            $this->writeInfoLog('Got english word and id from DB', [
                'english word' => $englishWordModel->word,
                'id' => $englishWordModel->id
            ]);
        } else {
            $englishWordModel = EnglishWord::create([
                'word' => $studyWord
            ]);
            $this->writeInfoLog('Not found english word and id in DB. Inserted new english word', [
                'english word' => $englishWordModel->word,
                'id' => $englishWordModel->id
            ]);
        }
        $this->addStudyWordId($userModel, $englishWordModel->id);
        $studyWordId = $englishWordModel->id;
        $this->writeInfoLog('Study word id added to the user', [
            'study_word' => $studyWord,
            'study word id' => $studyWordId,
            'user id' => $userModel->id
        ]);
        return $studyWordId;
    }

    /** Adds known words to a User.
     *
     * @param User $userModel
     * @param string $word
     * @param WordKind $wordKind
     * @param StatisticsService $statisticsService
     * @param HistoryRecordService $historyService
     */
    public function addKnownWord(
        User $userModel,
        string $word,
        WordKind $wordKind,
        StatisticsService $statisticsService,
        HistoryRecordService $historyService
    ): void {
        $englishWordModel = EnglishWord::where('word', $word)->first();

        $userModel->knownWords()->syncWithoutDetaching($englishWordModel->id);

        $this->writeInfoLog('Added known word to user', [
            'known word' => $word,
            'known word id' => $englishWordModel->id
        ]);

        $statisticsService->addRecord($userModel->id, WordStatus::known, $englishWordModel->id);

        $historyService->addRecord(
            $word,
            AnswerKind::right,
            WordStatus::known,
            $wordKind,
            $userModel->id,
            $englishWordModel ?->id,
            null
        );
    }

    /** Return a user model found by variable $telegramUserId
     *
     * @param string $telegramUserId
     * @return User|null
     */
    public function getUserByTelegramUserId(string $telegramUserId): ?User
    {
        $user = User::where('telegram_user_id', $telegramUserId)->first();
        $this->writeInfoLog('Got user model by telegramUserId', [
            'telegramUserId' => $telegramUserId,
            'user model' => $user
        ]);
        return $user;
    }



    /** Returns an array of ids of not known study words, using sent_study_words table.
     *
     * @param User $userModel
     * @return array
     */
    public function getSentStudyWordsIdsWithoutKnown(User $userModel): array
    {
        $knownWordsIds = $this->getKnownWordsIds($userModel);
        $sentStudyWordsIds = $this->getSentStudyWordsIds($userModel);
        if ($knownWordsIds !== false) {
            $sentStudyWordsIdsWithoutKnown = [];
            foreach ($sentStudyWordsIds as $sentStudyWordsId) {
                if (!in_array($sentStudyWordsId, $knownWordsIds)) {
                    $sentStudyWordsIdsWithoutKnown[] = $sentStudyWordsId;
                }
            }
        } else {
            $sentStudyWordsIdsWithoutKnown = $sentStudyWordsIds;
        }
        $this->writeInfoLog('Got sent study words ids without known words', [
            'result' => $sentStudyWordsIdsWithoutKnown
        ]);
        return $sentStudyWordsIdsWithoutKnown;
    }

    /** Returns an array of ids of not known study words, which are not in array knownWordsIds.
     *
     * Method use sent_study_words table.
     * @param User $userModel
     * @param array $knownWordsIds
     * @return array
     */
    public function getSentStudyWordsIdsWithoutKnownByKnownWordsIds(User $userModel, array $knownWordsIds): array
    {
        $sentStudyWordsIds = $this->getSentStudyWordsIds($userModel);
        if (sizeof($knownWordsIds) > 0) {
            $sentStudyWordsIdsWithoutKnown = [];
            foreach ($sentStudyWordsIds as $sentStudyWordsId) {
                if (!in_array($sentStudyWordsId, $knownWordsIds)) {
                    $sentStudyWordsIdsWithoutKnown[] = $sentStudyWordsId;
                }
            }
        } else {
            $sentStudyWordsIdsWithoutKnown = $sentStudyWordsIds;
        }
        $this->writeInfoLog('Got sent study words ids without known words', [
            'result' => $sentStudyWordsIdsWithoutKnown
        ]);
        return $sentStudyWordsIdsWithoutKnown;
    }

    /** Returns an array of ids of not known study words and without wrong answered.
     *
     * Method use sent_study_words table.
     * @param User $userModel
     * @param array $knownWordsIds
     * @param array $wrongAnsweredWordsIds
     * @return array
     */
    public function getSentStudyWordsIdsWithoutKnownAndWrongAnsweredByKnownWordsIds(
        User $userModel,
        array $knownWordsIds,
        array $wrongAnsweredWordsIds
    ): array {
        $sentStudyWordsIds = $this->getSentStudyWordsIds($userModel);
        if (sizeof($knownWordsIds) > 0 && sizeof($wrongAnsweredWordsIds) > 0) {
            $result = [];
            foreach ($sentStudyWordsIds as $sentStudyWordsId) {
                if (!in_array($sentStudyWordsId, $knownWordsIds) && !in_array($sentStudyWordsId, $wrongAnsweredWordsIds)) {
                    $result[] = $sentStudyWordsId;
                }
            }
        } elseif (sizeof($knownWordsIds) > 0 && sizeof($wrongAnsweredWordsIds) == 0) {
            $result = [];
            foreach ($sentStudyWordsIds as $sentStudyWordsId) {
                if (!in_array($sentStudyWordsId, $knownWordsIds)) {
                    $result[] = $sentStudyWordsId;
                }
            }
        } elseif (sizeof($knownWordsIds) == 0 && sizeof($wrongAnsweredWordsIds) > 0) {
            $result = [];
            foreach ($sentStudyWordsIds as $sentStudyWordsId) {
                if (!in_array($sentStudyWordsId, $wrongAnsweredWordsIds)) {
                    $result[] = $sentStudyWordsId;
                }
            }
        } else {
            $result = $sentStudyWordsIds;
        }
        $this->writeInfoLog('Got sent study words ids without known and wrong answered words', [
            'known words ids' => $knownWordsIds,
            'wrong answered words ids' => $wrongAnsweredWordsIds,
            'result' => $result
        ]);
        return $result;
    }


    /** Returns english words ids of sent words without known words, using sent_study_words table.
     *
     * @param User $userModel
     * @return array
     */
    public function getSentWordsIdsWithoutKnown(User $userModel): array
    {
        $knownWordsIds = $this->getKnownWordsIds($userModel);
        $sentWordsIds = $this->getSentWordsIds($userModel);
        if ($knownWordsIds !== false) {
            $sentWordsIdsWithoutKnown = [];
            foreach ($sentWordsIds as $sentWordsId) {
                if (!in_array($sentWordsId, $knownWordsIds)) {
                    $sentWordsIdsWithoutKnown[] = $sentWordsId;
                }
            }
        } else {
            $sentWordsIdsWithoutKnown = $sentWordsIds;
        }
        $this->writeInfoLog('Got sent words ids without known words', [
            'result' => $sentWordsIdsWithoutKnown
        ]);
        return $sentWordsIdsWithoutKnown;
    }

    /** Returns an array of ids of not known sent words and without wrong answered.
     * Method use sent_study_words table.
     *
     * @param User $userModel
     * @param array $knownWordsIds
     * @param array $wrongAnsweredWordsIds
     * @return array
     */
    public function getSentWordsIdsWithoutKnownAndWrongAnsweredByKnownWordsIds(
        User $userModel,
        array $knownWordsIds,
        array $wrongAnsweredWordsIds
    ): array {
        $sentWordsIds = $this->getSentWordsIds($userModel);
        if (sizeof($knownWordsIds) > 0 && sizeof($wrongAnsweredWordsIds) > 0) {
            $result = [];
            foreach ($sentWordsIds as $sentWordsId) {
                if (!in_array($sentWordsId, $knownWordsIds) && !in_array($sentWordsId, $wrongAnsweredWordsIds)) {
                    $result[] = $sentWordsId;
                }
            }
        } elseif (sizeof($knownWordsIds) > 0 && sizeof($wrongAnsweredWordsIds) == 0) {
            $result = [];
            foreach ($sentWordsIds as $sentWordsId) {
                if (!in_array($sentWordsId, $knownWordsIds)) {
                    $result[] = $sentWordsId;
                }
            }
        } elseif (sizeof($knownWordsIds) == 0 && sizeof($wrongAnsweredWordsIds) > 0) {
            $result = [];
            foreach ($sentWordsIds as $sentWordsId) {
                if (!in_array($sentWordsId, $wrongAnsweredWordsIds)) {
                    $result[] = $sentWordsId;
                }
            }
        } else {
            $result = $sentWordsIds;
        }
        $this->writeInfoLog('Got sent words ids without known and wrong answered words', [
            'known words ids' => $knownWordsIds,
            'wrong answered words ids' => $wrongAnsweredWordsIds,
            'result' => $result
        ]);
        return $result;
    }

    /** Returns an array of ids of not known sent words, which are not in array knownWordsIds.
     * Method use sent_study_words table.
     *
     * @param User $userModel
     * @param array $knownWordsIds
     * @return array
     */
    public function getSentWordsIdsWithoutKnownByKnownWordsIds(User $userModel, array $knownWordsIds): array
    {
        $sentWordsIds = $this->getSentWordsIds($userModel);
        if (sizeof($knownWordsIds) > 0) {
            $sentWordsIdsWithoutKnown = [];
            foreach ($sentWordsIds as $sentWordsId) {
                if (!in_array($sentWordsId, $knownWordsIds)) {
                    $sentWordsIdsWithoutKnown[] = $sentWordsId;
                }
            }
        } else {
            $sentWordsIdsWithoutKnown = $sentWordsIds;
        }
        $this->writeInfoLog('Got sent words ids without known words', [
            'result' => $sentWordsIdsWithoutKnown
        ]);
        return $sentWordsIdsWithoutKnown;
    }


    /** Creates a new user's account.
     *
     * @param string $firstName
     * @param string|null $lastName
     * @param string|null $telegramUserName
     * @param string $telegramUserId
     * @param string $languageCode
     * @param string $chatId
     * @param string $type
     * @return User
     */
    public function createUser(
        string $firstName,
        ?string $lastName,
        ?string $telegramUserName,
        string $telegramUserId,
        string $languageCode,
        string $chatId,
        string $type
    ): User {
        $user = User::create([
            'email' => $telegramUserId . 'temporary@gamil.com',
            'password' => Hash::make($telegramUserId),
            'telegram_user_id' => $telegramUserId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'telegram_user_name' => $telegramUserName,
            'language_code' => $languageCode,
            'chat_id' => $chatId,
            'type' => $type,
            'eng_words_per_twitch' => config('user.eng_words_per_twitch'),
            'english_words_week_and_times_sending_conf' => json_encode([
                    '1' => '09:00'
                ]), //Mon at 9:00
            'quiz_week_and_times_sending_conf' => json_encode([
                    '1' => '09:00'
                ]),
            'quiz_quantity_sending_conf' => json_encode([
                '1' => 1
            ])
        ]);

        $this->writeInfoLog('User has created', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'telegram_user_name' => $telegramUserName,
            'language_code' => $languageCode,
            'chat_id' => $chatId,
            'type' => $type,
            'english_words_week_and_times_sending_conf' => json_encode([
                    '1' => '09:00'
                ]),
            'quiz_week_and_times_sending_conf' => json_encode([
                    '1' => '09:00'
                ])
        ]);

        $this->writeInfoLog('New user started using', [
                'telegram_user_id' => $telegramUserId,
                'telegram_user_name' => $telegramUserName,
        ], isAllowedSendToTlg: true);

        return $user;
    }

    /** Deletes user's account.
     *
     * @param string $fromId
     * @return int
     */
    public function deleteUser(string $fromId): int
    {
        $this->writeInfoLog('Deleting the user', [
            'user\'s from id' => $fromId
        ]);

        $userModel = User::where('telegram_user_id', $fromId)->first();
        $userId = $userModel->id;

        $userModel->delete();

        $this->writeInfoLog('Stopped using and deleted', [
            'telegram_user_id' => $fromId
        ], isAllowedSendToTlg: true);

            return $userId;
    }

    /** Gets portion of study words ids without ids in exceptIds param.
     *
     * @param array $exceptIds
     * @param User $userModel
     * @param int $portion
     * @return mixed
     */
    public function getPortionOfStudyWordsIdsExcept(array $exceptIds, User $userModel, int $portion = 1): mixed
    {
        $studyWordsIds = $this->getStudyWordsIds($userModel);
        $studyWordsIdsExcept = array_diff($studyWordsIds, $exceptIds);
        $result = array_slice($studyWordsIdsExcept, 0, $portion);

        $this->writeInfoLog('Got portion of study words of user except entered except ids', [
            'except ids' => $exceptIds,
            'id of user' => $userModel->id,
            'portion' => $portion,
            'study words ids without except ids' => $studyWordsIdsExcept,
            'result' => $result
        ]);
        return $result;
    }

    /** Gets study words ids without ids in exceptIds param.
     *
     * @param array $exceptIds
     * @param User $userModel
     * @return array
     */
    public function getStudyWordsIdsExcept(array $exceptIds, User $userModel): array
    {
        $studyWordsIds = $this->getStudyWordsIds($userModel);
        $studyWordsIdsExcept = array_diff($studyWordsIds, $exceptIds);
        $this->writeInfoLog('Got portion of study words of user except entered except ids', [
            'except ids' => $exceptIds,
            'id of user' => $userModel->id,
            'study words ids without except ids' => $studyWordsIdsExcept,
            'result' => $studyWordsIdsExcept
        ]);
        return $studyWordsIdsExcept;
    }

    /** Changes study words ids.
     *
     * @param User $userModel
     * @param int $fromId
     * @param int $toId
     */
    public function changeStudyWordsId(User $userModel, int $fromId, int $toId): void
    {
        DB::connection()->enableQueryLog();
        $this->writeInfoLog('Change study words id method executing', [
            'which id' => $fromId,
            'to id' => $toId
        ]);

        $userModel->studyWords()->detach($fromId);

        $userModel->studyWords()->attach($toId);

        $this->writeInfoLog('Changed study words id', [
            'deb query' => DB::getQueryLog()
        ]);
    }

    /** Checks if user has sent study words, entered in studyWord param.
     *
     * @param User $userModel
     * @param string $studyWord
     * @return bool
     * @throws \Exception
     */
    public function hasSentStudyWord(User $userModel, string $studyWord): bool
    {
        $this->writeInfoLog('HasSentStudyWord', [
            'user id' => $userModel->id,
            'study word' => $studyWord,
        ]);
        $studyWord = str_ireplace('_', ' ', $studyWord);
        $englishWordModel = $this->englishWordService->getEnglishWordModel($studyWord);
        if (!is_null($englishWordModel)) {
            $sentStudyWordsIds = $this->getSentStudyWordsIds($userModel);
            return in_array($englishWordModel->id, $sentStudyWordsIds);
        } else {
            $this->writeErrorLog('Did not get english word model by word', [
                'word' => $studyWord
            ]);
            throw new \Exception('Did not get english word model by word');
        }
    }

    /** Creates the is_answer_received param of answer of quiz.
     *
     * @param User $userModel
     * @param int $waitForAnswerTime
     */
    public function createQuizAnswerIsReceivedParam(User $userModel, int $waitForAnswerTime = 30): void
    {
        $value = false;
        Cache::put('quiz_answer_is_received_param_' . $userModel->id, $value, $waitForAnswerTime);
        $this->writeInfoLog('Created quiz_answer_is_received_param', [
            'value set to cache' => $value,
            'set expiration time' => $waitForAnswerTime
        ]);
    }

    /** Sets the is_answer_received param of answer of quiz.
     *
     * @param User $userModel
     */
    public function setQuizAnswerIsReceivedParamReceived(User $userModel): void
    {
        Cache::put('quiz_answer_is_received_param_' . $userModel->id, true);
        $this->writeInfoLog('Set quiz_answer_is_received_param received', [
            'value set to cache' => true
        ]);
    }

    /** Gets the is_answer_received param of answer of quiz.
     *
     * @param User $userModel
     * @return bool|null
     */
    public function getQuizAnswerIsReceivedParam(User $userModel): bool|null
    {
        $isAnswerReceived = Cache::get('quiz_answer_is_received_param_' . $userModel->id);

        $this->writeInfoLog('Get quiz_answer_is_received_param', [
            'value from cache' => $isAnswerReceived
        ]);
        return $isAnswerReceived;
    }

    /** Removes known words of a User.
     *
     * @param User $userModel
     * @param array $knownWordsIds
     */
    public function removeKnownWords(User $userModel, array $knownWordsIds): void
    {
        $userModel->knownWords()->detach($knownWordsIds);
        $this->writeInfoLog('Removed user\'s known words ids', [
            'removed known words ids' => $knownWordsIds,
        ]);
    }

    /** Gets known words of a User.
     *
     * @param User $userModel
     * @return array
     */
    public function getKnownWords(User $userModel): array
    {
        $result = DB::table('english_words')
            ->join('known_words', 'known_words.english_word_id', '=', 'english_words.id')
            ->join('users', 'users.id', '=', 'known_words.user_id')
            ->whereRaw('users.id = ?', $userModel->id)
            ->select(['english_words.word'])
            ->get()->pluck('word')->toArray();

        $this->writeInfoLog('Got user\'s known words', [
            'result' => $result
        ]);
        return $result;
    }

    /** Gets the user model by chat id.
     *
     * @param string $chatId
     * @return User|null
     */
    public function getUsermodelByChatId(string $chatId): ?User
    {
        $result = User::where('chat_id', $chatId)->first();

        $this->writeInfoLog('Got user model by chat id', [
            'chat id' => $chatId
        ]);

        return $result;
    }

    /**Updates user's language config.
     *
     * @param User $userModel
     * @param string $language
     */
    public function updateLanguage(User $userModel, string $language): void
    {
        $userModel->update(['language' => $language]);

        $this->writeInfoLog('Updated user\'s language', [
            'new language' => $language
        ]);
    }

    /** Deletes user's english words sending config by caht id.
     *
     * @param string $chatId
     */
    public function deleteEnglishWordsSendingConfigByChatId(string $chatId): void
    {
        $userModel = User::where('chat_id', $chatId)->first();
        $userModel->update([
            'english_words_week_sending_conf ' => 0,
            'english_words_week_and_times_sending_conf' => null
        ]);
    }
}

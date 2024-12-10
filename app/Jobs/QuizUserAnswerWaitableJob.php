<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\Helpers\FieldId;
use App\Services\History\HistoryMessageService;
use App\Services\History\HistoryRecordService;
use App\Services\Cache\QuizCacheService;
use App\Services\StatisticsService;
use App\Services\StudyWordsService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Traits\LoggerTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Enums\MessageType;
use App\Enums\WordStatus;
use Illuminate\Bus\Batchable;
use DateTime;
use Illuminate\Support\Facades\App;

class QuizUserAnswerWaitableJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use LoggerTrait;
    use Batchable;

    private bool $isFirstQuizForToday;
    private string $chatId;
    private User $userModel;
    private DateTime $nowDateTime;
    private TelegramService $telegramService;
    private StatisticsService $statisticsService;
    private QuizCacheService $quizCacheService;
    private string $customBatchId;
    private string $timeAsId;

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): DateTime
    {
        return now()->addMinutes(4); //was 2
    }


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $userModel, string $timeAsId, bool $isFirstQuizForToday = false)
    {

        $this->customBatchId = FieldId::makeFromModel($userModel, $timeAsId);
        $this->writeInfoLog('Quiz user answer waitable job has started', [
            'id of user' => $userModel->id,
            'time as id' => $timeAsId,
            'custom batch id' => $this->customBatchId
        ]);
        $this->userModel = $userModel;
        $this->isFirstQuizForToday = $isFirstQuizForToday;
        $this->timeAsId = $timeAsId;
    }

    /**
     * Execute the job.
     *
     * @param UserService $userService
     * @param StudyWordsService $studyWordsService
     * @param TelegramService $telegramService
     * @param EnglishWordService $englishWordService
     * @param HistoryMessageService $historyMessageService
     * @param StatisticsService $statisticsService
     * @param QuizCacheService $quizCacheService
     * @param LanguageCacheService $languageCacheService
     * @throws \Exception
     */
    public function handle(
        UserService $userService,
        StudyWordsService $studyWordsService,
        TelegramService $telegramService,
        EnglishWordService $englishWordService,
        HistoryMessageService $historyMessageService,
        StatisticsService $statisticsService,
        QuizCacheService $quizCacheService,
        LanguageCacheService $languageCacheService
    ) {
        if ($this->batch()->cancelled()) {
            $this->writeInfoLog('Batch has cancelled.');
            return;
        }

        $quizRequiredMinVariants = config('quiz.required_min_variants');
        $quizRequiredMaxVariants = config('quiz.required_max_variants');
        $this->chatId = $this->userModel->chat_id;
        $this->telegramService = $telegramService;
        $this->nowDateTime = new \DateTime('now');
        $this->statisticsService = $statisticsService;
        $this->quizCacheService = $quizCacheService;
        $customBatchId = $this->customBatchId;

        $userLanguage = $languageCacheService->getLanguageInsteadFromModel($this->userModel);
        App::setLocale($userLanguage);

        $userService->createQuizAnswerIsReceivedParam($this->userModel, config('quiz.user_answer_waiting_seconds'));

        $wrongAnsweredWordsIds = $quizCacheService->getWrongAnsweredWordsIds($customBatchId);

        $this->writeInfoLog('Quiz user answer waitable job execution', [
            'job id' => $this->job->getJobId(),
            'id of user' => $this->userModel->id,
            'chat id' => $this->chatId,
            'quiz required' => [
                'min variants' => $quizRequiredMinVariants,
                'max variants' => $quizRequiredMaxVariants,
            ],
            'quiz current max answers' => $this->userModel->quiz_max_answers,
            'started at' => $this->nowDateTime->format('Y-m-d H:i:s'),
            'is first quiz for today' => $this->isFirstQuizForToday,
            'wrong answered words ids' => $wrongAnsweredWordsIds,
            'time as batch id' => $customBatchId
        ]);

        $knownWordsIds = $userService->getKnownWordsIds($this->userModel);

        $sentStudyWordsIdsWithoutKnownAndWrongAnswered = $userService
            ->getSentStudyWordsIdsWithoutKnownAndWrongAnsweredByKnownWordsIds(
                $this->userModel,
                $knownWordsIds,
                $wrongAnsweredWordsIds
            );
        $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered =
            count($sentStudyWordsIdsWithoutKnownAndWrongAnswered);

        $this->writeInfoLog('Got sent study words ids quantity, without known', [
            'quantity' => $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered,
            'id of user' => $this->userModel->id
        ]);

        $sentWordsIdsWithoutKnownAndWrongAnswered = $userService
            ->getSentWordsIdsWithoutKnownAndWrongAnsweredByKnownWordsIds(
                $this->userModel,
                $knownWordsIds,
                $wrongAnsweredWordsIds
            );

        $sentWordsQuantityVariantsWithoutKnownAndWrongAnswered = count($sentWordsIdsWithoutKnownAndWrongAnswered);

        $this->writeInfoLog('Got sent words quantity, without wrong answered and without portion of known', [
            'quantity' => $sentWordsQuantityVariantsWithoutKnownAndWrongAnswered,
            'id of user' => $this->userModel->id
        ]);

        if (
            ($sentWordsQuantityVariantsWithoutKnownAndWrongAnswered
            + $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered)
            >=
            $quizRequiredMinVariants
        ) {
            $log = [
                'sent words quantity without known and wrong answered' =>
                    $sentWordsQuantityVariantsWithoutKnownAndWrongAnswered,
                'sent study words quantity variants without known and wrong answered' =>
                    $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered,
                'sent words and study variants quantity, both without known and wrong answered' =>
                    $sentWordsQuantityVariantsWithoutKnownAndWrongAnswered +
                    $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered,
            ];

            $log['required min quantity quiz variants'] = $quizRequiredMinVariants;
            $log['id of user'] = $this->userModel->id;
            $this->writeInfoLog('Handling a quiz with study words and english words.', $log);

            $englishWordsIdsToTranslate = $sentStudyWordsIdsWithoutKnownAndWrongAnswered;

            for (
                $i = $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered;
                $i <= $this->userModel->quiz_max_answers;
                $i++
            ) {
                $sentWordId = current($sentWordsIdsWithoutKnownAndWrongAnswered);
                if ($sentWordId) {
                    $englishWordsIdsToTranslate[] = $sentWordId;
                    next($sentWordsIdsWithoutKnownAndWrongAnswered);
                }
            }

            $this->writeInfoLog('Got words ids to translate from study words and english words', [
                'english words ids to translate' => $englishWordsIdsToTranslate
            ]);

            $englishWordsIdsToTranslateCount = count($englishWordsIdsToTranslate);
            $randKey = mt_rand(0, $englishWordsIdsToTranslateCount - 1);
            $englishWordsIdToTranslateVariant = $englishWordsIdsToTranslate[$randKey];

            $this->writeInfoLog('Got random', [
                'range start' => 0,
                'range end' => $englishWordsIdsToTranslateCount - 1,
                'random key' => $randKey,
                'english word variant id by key' => $englishWordsIdToTranslateVariant,
                'id of user' => $this->userModel->id
            ]);


            $russianVariants = $englishWordService->translateById($englishWordsIdToTranslateVariant);
            if (is_null($russianVariants) || false === $russianVariants) {
                throw new \Exception('Failed to translate english word by id');
            }

            $buttonRows = $this->answersButtonsCreate(
                $englishWordsIdsToTranslate,
                $englishWordsIdToTranslateVariant,
                $studyWordsService,
                $quizRequiredMinVariants,
                $this->userModel->quiz_max_answers,
                $this->timeAsId,
                $answerVariants
            );

            $this->createAnswerTextAndSendTelegramMessage(
                $russianVariants,
                $this->chatId,
                $buttonRows,
                $telegramService,
                $this->isFirstQuizForToday,
                $sentMessageId
            );

            $telegramService->setButtonsStructToCache($this->userModel->id, $customBatchId, $buttonRows);

            $historyMessageService->addRecordFromArray(
                $russianVariants,
                $this->userModel->id,
                MessageType::info,
                __("history-records.quiz") . ': ',
                'answer variants: ',
                $answerVariants
            );

            $this->waitForAnswer(
                $userService,
                $historyMessageService,
                $statisticsService,
                $customBatchId,
                $sentMessageId,
                $englishWordsIdToTranslateVariant
            );

            return;
        } else {
            $this->writeInfoLog('Cannot prepare a quiz. Sent english words and study words quantity too less.', [
                'sent study and english words quantity, both without knowk and wrong answered' =>
                    $sentWordsQuantityVariantsWithoutKnownAndWrongAnswered +
                    $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered,
                'required min quantity quiz variants' => $quizRequiredMinVariants,
                'id of user' => $this->userModel->id
            ]);

            if ($sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered >= $quizRequiredMinVariants) {
                $this->writeInfoLog('Handling a quiz with study words only.', [
                    'sent study words quantity without known and wrong answered' =>
                        $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered,
                    'quiz required' => [
                        'min variants' => $quizRequiredMinVariants,
                        'max variants' => $quizRequiredMaxVariants,
                    ]
                ]);

                $russianTranslateVariants = $studyWordsService
                    ->translateSeveralByStudyWordIds($sentStudyWordsIdsWithoutKnownAndWrongAnswered);
                $randKey = mt_rand(0, $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered - 1);
                $studyWordIdVariant = $sentStudyWordsIdsWithoutKnownAndWrongAnswered[$randKey];

                $this->writeInfoLog('Got random', [
                    'range start' => 0,
                    'range end' => $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered - 1,
                    'random key' => $randKey,
                    'study word variant id by key' => $studyWordIdVariant,
                    'id of user' => $this->userModel->id
                ]);

                $buttonRows = $this->answersButtonsCreate(
                    $sentStudyWordsIdsWithoutKnownAndWrongAnswered,
                    $studyWordIdVariant,
                    $studyWordsService,
                    $quizRequiredMinVariants,
                    $this->userModel->quiz_max_answers,
                    $this->timeAsId,
                    $answerVariants
                );

                $russianVariant = $russianTranslateVariants[$studyWordIdVariant];
                $this->createAnswerTextAndSendTelegramMessage(
                    $russianVariant,
                    $this->chatId,
                    $buttonRows,
                    $telegramService,
                    $this->isFirstQuizForToday,
                    $sentMessageId
                );

                $telegramService->setButtonsStructToCache($this->userModel->id, $customBatchId, $buttonRows);

                $historyMessageService->addRecordFromArray(
                    $russianVariant,
                    $this->userModel->id,
                    MessageType::info,
                    __("history-records.quiz") . ': ',
                    'answer variants: ',
                    $answerVariants
                );

                $this->waitForAnswer(
                    $userService,
                    $historyMessageService,
                    $statisticsService,
                    $customBatchId,
                    $sentMessageId,
                    $studyWordIdVariant
                );
                return;
            } else {
                if ($this->batch()->cancelled()) {
                    $this->writeInfoLog('Batch has cancelled.');
                    return;
                }

                $this->writeInfoLog('Cannot prepare a quiz. Sent study words quantity too less.', [
                    'sent study words quantity without known and wrong answered' => $sentStudyWordsQuantityVariantsWithoutKnownAndWrongAnswered,
                    'required min quantity quiz variants' => $quizRequiredMinVariants,
                    'id of user' => $this->userModel->id
                ]);
            }

            $buttons = [
                [
                    [
                        'text' => __('Yes'),
                        'callback_data' => '#twitch start ' . $this->userModel->id . ' ' . $this->timeAsId,
                    ],
                    [
                        'text' => __('No'),
                        'callback_data' => '#twitch discard ' . $this->userModel->id . ' ' . $this->timeAsId,
                    ]
                ]
            ];
            $telegramService->sendMessageAndButtons(
                $this->userModel->chat_id,
                __("I can’t take the test, I’ve run out of words. Send new ones?"),
                //'Не могу провести тест, закончились слова. Прислать новые?',
                $buttons
            );

            $telegramService->setButtonsStructToCache($this->userModel->id, $customBatchId, $buttons);

            $this->batch()->cancel();
        }
    }

    /** Helper returns item of associative array by position. If not found by position returns null.
     * @param array $source
     * @param int $position
     * @param ?int &$keyOfItem = null
     * @return mixed|null
     */
    private function getAssocArrayItemByPosition(array $source, int $position, ?int &$keyOfItem = null): mixed
    {
        $result = null;
        $i = 0;
        foreach ($source as $key => $item) {
            if ($i == $position) {
                $result = $item;
                $keyOfItem = $key;
                break;
            }
            $i++;
        }
        $this->writeInfoLog('Got item of assoc. array by position', [
            'array' => $source,
            'position' => $position,
            'key of position' => $keyOfItem,
            'result' => $result
        ]);
        return $result;
    }

    /** Helper creates array of telegram answer buttons with english words variants.
     * @param array $englishWordsIds
     * @param int $rightEnglishWordId
     * @param StudyWordsService $studyWordsService
     * @param int $quizRequiredMinVariants
     * @param int $quizRequiredMaxVariants
     * @param  string $timeAsId
     * @param  ?array &$answerVariants
     * @return array
     */
    private function answersButtonsCreate(
        array $englishWordsIds,
        int $rightEnglishWordId,
        StudyWordsService $studyWordsService,
        int $quizRequiredMinVariants,
        int $quizRequiredMaxVariants,
        string $timeAsId,
        ?array &$answerVariants = null
    ): array {
        $isSetRightAnswer = false;
        $englishVariants = $studyWordsService->getWordsByIds($englishWordsIds);
        $rightAnswerWord = $englishVariants[$rightEnglishWordId];

        if (count($englishWordsIds) < $quizRequiredMaxVariants) {
            $quizRequiredMaxVariants = count($englishWordsIds);
        }
        $rightAnswerPosition = mt_rand(0, $this->userModel->quiz_max_answers - 1);

        $this->writeInfoLog('Got english variants and right answer word', [
            'english words ids' => $englishWordsIds,
            'english variants' => $englishVariants,
            'right answer word id' => $rightEnglishWordId,
            'right answer word' => $rightAnswerWord,
            'right answer position range' => [
                'start' => 0,
                'end' => $this->userModel->quiz_max_answers - 1,
            ],
            'right answer position' => $rightAnswerPosition,
            'id of user' => $this->userModel->id
        ]);

        $i = 0;
        $j = 0;
        $l = 0;
        $buttonRows = [];
        $prevRandomKeys = [];

        foreach ($englishWordsIds as $englishWordsId) {
            if ($l == $this->userModel->quiz_max_answers) {
                break;
            }
            if ($i == 2) {
                break;
            }
            if ($j == $quizRequiredMinVariants) {
                $j = 0;
                $i++;
            }

            $randomKey = mt_rand(0, $this->userModel->quiz_max_answers - 1);
            $isRandomKeyAlreadyExist = in_array($randomKey, $prevRandomKeys);

            $this->writeInfoLog('Got random key', [
                'randomKey' => $randomKey,
                'prevRandomKeys array' => $prevRandomKeys,
                'randomKey is already in prevRandomKeys array' => $isRandomKeyAlreadyExist
            ]);

            if ($isRandomKeyAlreadyExist) {
                do {
                    $randomKey = mt_rand(0, count($englishVariants) - 1);
                } while (in_array($randomKey, $prevRandomKeys));

                $this->writeInfoLog('Rerandomed randomKey', [
                    'rerandomed randomKey' => $randomKey
                ]);
            }

            $prevRandomKeys[] = $randomKey;

            $this->writeInfoLog('random key and prevRandomKeys array', [
                'random key' => $randomKey,
                'prevRandomKeys array' => $prevRandomKeys
            ]);

            $englishVariant = $this->getAssocArrayItemByPosition(
                $englishVariants,
                $randomKey,
                $englishVariantWordId
            );
            $answerVariants[] = $englishVariant;

            if ($englishVariant == $rightAnswerWord) {
                $tag = '#rightAnswer';
                $isSetRightAnswer = true;
            } else {
                $tag = '#wrongAnswer';
            }

            $rightAnswerWordInCommandLine = str_ireplace(' ', '_', $rightAnswerWord);
            $englishVariantInCommandLine = str_ireplace(' ', '_', $englishVariant);

            $buttonRows[$i][$j] = [
                'text' => $englishVariant,
                'callback_data' => $tag . ' ' . $rightAnswerWordInCommandLine . ' ' . $englishVariantInCommandLine .
                    ' ' . $timeAsId . ' ' . $rightEnglishWordId
            ];

            $j++;
            $l++;
        }
        $this->writeInfoLog('Done buttons rows array', [
            'button rows array' => $buttonRows,
        ]);

        if (!$isSetRightAnswer) {
            $l = 0;
            $i = 0;
            $j = 0;
            foreach ($englishWordsIds as $englishWordsId) {
                if ($l == $quizRequiredMaxVariants) {
                    break;
                }
                if ($i == 2) {
                    break;
                }
                if ($j == $quizRequiredMinVariants) {
                    $j = 0;
                    $i++;
                }
                if ($l == $rightAnswerPosition) {
                    $englishVariantWordId = $rightEnglishWordId;
                    $buttonRows[$i][$j] = [
                        'text' => $rightAnswerWord,
                        'callback_data' => '#rightAnswer ' . $rightAnswerWordInCommandLine . ' ' .
                            $rightAnswerWordInCommandLine . ' ' . $timeAsId . ' ' . $rightEnglishWordId
                    ];
                    $answerVariants[$l] = $rightAnswerWord;
                }
                $j++;
                $l++;
            }
            $this->writeInfoLog('Right answer is out of range. Redefining', [
                'right answer word' => $rightAnswerWord,
                'button rows after' => $buttonRows,
            ]);
        }
        return $buttonRows;
    }

    /** Helper for creating the text of message and send the message to telegram chat.
     * @param array $russianVariants
     * @param string $chatId
     * @param array $buttonRows
     * @param TelegramService $telegramService
     * @param bool $isFirstQuizForToday
     * @param ?int &$sentMessageId
     * @return void
     */
    public function createAnswerTextAndSendTelegramMessage(
        array $russianVariants,
        string $chatId,
        array $buttonRows,
        TelegramService $telegramService,
        bool $isFirstQuizForToday,
        ?int &$sentMessageId = null
    ): void {
        $russianText = '';
        foreach ($russianVariants as $variant) {
            $russianText .= '- ' . $variant . PHP_EOL;
        }

        if ($isFirstQuizForToday) {
            $AnswerText = __("It's time to test your knowledge. How to translate") . ':' . PHP_EOL;
            //Пришло время проверить твои знания. Как переводится:
        } else {
            $AnswerText = __("How to translate") . ':' . PHP_EOL;
            //'Как переводится: '
        }
        $telegramService->sendMessageAndButtons(
            $chatId,
            $AnswerText . $russianText . ' ?',
            $buttonRows,
            $sentMessageId
        );

        $this->writeInfoLog('createAnswerTextAndSendTelegramMessage method has executed', [
            'sent message id' => $sentMessageId,
        ]);
    }

    private function waitForAnswer(
        UserService $userService,
        HistoryMessageService $historyMessageService,
        StatisticsService $statisticsService,
        string $messageIdInCache,
        int $sentMessageId,
        int $rightAnswerWordId
    ) {
        $sleepSeconds = 5;
        $tryNumber = 0;
        $maxTriesNumber = intdiv(config('quiz.user_answer_waiting_seconds'), $sleepSeconds);

        $this->writeInfoLog('QuizUserAnswerWaitableJob. waitForAnswer method executing', [
            'max try number' => $maxTriesNumber,
            'sent message id' => $sentMessageId,
            'right answer word id' => $rightAnswerWordId
        ]);

        while (true) {
            $this->writeInfoLog('QuizUserAnswerWaitableJob. waitForAnswer method executing', [
                'number of check answer try' => ++$tryNumber
            ]);

            sleep($sleepSeconds); //10

            $isAnswerReceived = $userService->getQuizAnswerIsReceivedParam($this->userModel);

            if ($isAnswerReceived === true) {
                $this->writeInfoLog('Answer received');
                break;
            }

            if ($tryNumber >= $maxTriesNumber) {
                $this->writeInfoLog('Answer has not received');
                $this->writeInfoLog(
                    'QuizUserAnswerWaitableJob has cancelled. Has not received answer from user.'
                );

                $this->telegramService->sendMessage(
                    $this->chatId,
                    __("The response time has expired. I will count the answer as incorrect") . '.'
                );
                //'Время ответа закончилось. Я зачту ответ как неправильный.'

                $this->quizCacheService->setWrongAnsweredQuestionRightWordId($messageIdInCache, $rightAnswerWordId);

                $statisticsService->addRecord($this->userModel->id, WordStatus::unknown);

                $historyMessageService->addRecord(
                    __("The response time has expired. The answer is incorrect") . '.',
                    $this->userModel->id,
                    MessageType::error
                );
                //'Время ответа закончилось. Ответ неправильный.'

                $buttonsStruct = $this->telegramService->getButtonsStructFromCacheAndFree(
                    $this->userModel->id,
                    $messageIdInCache
                );
                $this->telegramService->disableAllButtonsCallbackOfMessage(
                    $this->chatId,
                    $sentMessageId,
                    $buttonsStruct
                );

                sleep(6);
                break;
            }
        }
    }
}

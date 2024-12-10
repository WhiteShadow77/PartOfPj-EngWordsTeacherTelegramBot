<?php

namespace App\Jobs;

use App\Enums\SentWordsKind;
use App\Events\EnglishWordTranslateEvent;
use App\Events\UntranslatedStudyWordEvent;
use App\Exceptions\NotFoundEnglishNoun;
use App\Exceptions\TranslateRemoteServiceException;
use App\Models\EnglishWord;
use App\Models\User;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\ForeignService;
use App\Services\Cache\MessageCacheService;
use App\Services\RussianWordService;
use App\Services\UserService;
use App\Services\History\HistoryMessageService;
use App\Traits\LoggerTrait;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Enums\MessageType;
use Illuminate\Support\Facades\App;
use Predis\Command\Redis\APPEND;

class TranslateStudyWordJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use LoggerTrait;

    private int|string $parentJobId;
    public int $tries;
    public int $lastEngWordId;
    private int $doseIndex;
    public string $message;
    private UserService $userService;
    private User $user;

    private EnglishWord $englishWordModel;

    private RussianWordService $russianWordService;
    private EnglishWordService $englishWordService;
    private MessageCacheService $messageCacheService;
    private bool $changeToNoun;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct(
        int|string $parentJobId,
        UserService $userService,
        User $user,
        int $doseIndex,
        EnglishWord $englishWordModel,
        RussianWordService $russianWordService,
        EnglishWordService $englishWordService,
        MessageCacheService $messageCacheService,
        bool $changeToNoun = true
    ) {
        $this->writeInfoLog('Translate study word job starting', [
            'user id' => $user->id
        ]);
        $this->parentJobId = $parentJobId;
        $this->tries = config('job.translate_study_words_tries_count');
        $this->userService = $userService;
        $this->user = $user;
        $this->russianWordService = $russianWordService;
        $this->englishWordService = $englishWordService;
        $this->doseIndex = $doseIndex;
        $this->englishWordModel = $englishWordModel;
        $this->message = '';
        $this->messageCacheService = $messageCacheService;
        $this->changeToNoun = $changeToNoun;
    }

    /** Execute the job.
     * @param ForeignService $foreignService
     * @param HistoryMessageService $historyMessageService
     * @param LanguageCacheService $languageCacheService
     * @return null
     * @throws TranslateRemoteServiceException
     * @throws NotFoundEnglishNoun
     * @throws Exception
     */
    public function handle(
        ForeignService $foreignService,
        HistoryMessageService $historyMessageService,
        LanguageCacheService $languageCacheService
    ) {
        $this->writeInfoLog('Translate study word job execution', [
            'job id' => $this->job->getJobId(),
            'word' => $this->englishWordModel->word,
            'change to noun' => $this->changeToNoun
        ]);

        $source = $foreignService->getSource($this->englishWordModel->word);
        $translations = $source->translateToRussian();
        if (false !== $translations) {
            if ($this->changeToNoun) {
                $this->writeInfoLog('Change a word to noun case', [
                    'study word' => $this->englishWordModel->word
                ]);
                $englishWordNoun = $source->getNoun();
                if ($englishWordNoun !== false) {
                    if ($englishWordNoun != $this->englishWordModel->word) {
                        $this->writeInfoLog('Study word and it\'s noun are different case', [
                            'study word' => $this->englishWordModel->word,
                            'study word noun' => $englishWordNoun
                        ]);
                        $englishWordNounModel = $this->englishWordService->getEnglishWordModelIfExist($englishWordNoun);
                        if ($englishWordNounModel !== false) {
                            $transcription = $source->getTranscription();
                            if ($transcription !== false) {
                                $pronunciationFileNamesAndDirs = $source->getMp3FileNamesAndDirs();
                                if ($pronunciationFileNamesAndDirs !== false) {
                                    event(new EnglishWordTranslateEvent(
                                        $englishWordNounModel,
                                        $transcription,
                                        $translations,
                                        $pronunciationFileNamesAndDirs,
                                        $source
                                    ));
                                    $this->userService->addSentStudyWordId($this->user, $englishWordNounModel->id);
                                    $this->userService->changeStudyWordsId(
                                        $this->user,
                                        $this->englishWordModel->id,
                                        $englishWordNounModel->id
                                    );

                                    $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId(
                                        $this->user->chat_id
                                    );
                                    App::setLocale($userLanguage);

                                    $message = $this->englishWordModel->word .
                                        ' (' . __("translation found for") . '  \'' .
                                        lcfirst($englishWordNoun) . '\')' .
                                        PHP_EOL;

                                    $this->messageFormate($translations, $message);

                                    $ukPronFileNameAndDir = storage_path() . '/app/public' . $englishWordNounModel->uk_pron_file;
                                    $this->messageCacheService->addToMessageWithFilePathUsingSortedSet(
                                        $this->parentJobId,
                                        $this->doseIndex,
                                        $message,
                                        $englishWordNounModel->word,
                                        $ukPronFileNameAndDir
                                    );
                                } else {
                                    throw new Exception(
                                        'Have not got pron. file names and dirs for word: ' . $englishWordNoun
                                    );
                                }
                            } else {
                                throw new Exception('Have not got transcription of word: ' . $englishWordNoun);
                            }
                        } else {
                            $this->englishWordService
                                ->addEnglishWordAddRussianTranslatesAndAttach($englishWordNoun, $translations);
                        }
                    } else {
                        $this->writeInfoLog('Study word and it\'s noun are equal case', [
                            'study word' => $this->englishWordModel->word,
                            'study word noun' => $englishWordNoun
                        ]);
                        $transcription = $source->getTranscription();
                        if ($transcription !== false) {
                            $pronunciationFileNamesAndDirs = $source->getMp3FileNamesAndDirs();
                            if ($pronunciationFileNamesAndDirs !== false) {
                                $this->translationsPrepareToMessageAndSend(
                                    $transcription,
                                    $translations,
                                    $pronunciationFileNamesAndDirs,
                                    $source
                                );
                            } else {
                                throw new Exception(
                                    'Have not got pron. file names and dirs for word: ' . $this->englishWordModel->word
                                );
                            }
                        } else {
                            throw new Exception(
                                'Have not got transcription of word: ' . $this->englishWordModel->word
                            );
                        }
                    }
                } else {
                    throw new NotFoundEnglishNoun($this->englishWordModel->word);
                }
            } else {
                $transcription = $source->getTranscription();
                if ($transcription !== false) {
                    $pronunciationFileNamesAndDirs = $source->getMp3FileNamesAndDirs();
                    if ($pronunciationFileNamesAndDirs !== false) {
                        $this->translationsPrepareToMessageAndSend(
                            $transcription,
                            $translations,
                            $pronunciationFileNamesAndDirs,
                            $source
                        );
                    } else {
                        throw new Exception(
                            'Have not got pron. file names and dirs for word: ' . $this->englishWordModel->word
                        );
                    }
                } else {
                    throw new Exception(
                        'Have not got transcription of word: ' . $this->englishWordModel->word
                    );
                }
            }
        } else {
            $this->writeInfoLog('Without change a word to noun case', [
                'study word' => $this->englishWordModel->word
            ]);

            $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($this->user->chat_id);
            App::setLocale($userLanguage);

            event(new UntranslatedStudyWordEvent($this->user, $this->englishWordModel));
            $message = __("For") . ' \'' . $this->englishWordModel->word . '\' ' . __("translation unknown")
                . PHP_EOL . PHP_EOL;
            $this->messageCacheService->addToMessageWithFilePathUsingSortedSet(
                $this->parentJobId,
                $this->doseIndex,
                $message,
                $this->englishWordModel->word
            );

            $this->userService->addUntranslatedStudyWordId($this->user, $this->englishWordModel->id);
            $historyMessageService->addRecord($message, $this->user->id, MessageType::error);
        }
        return null;
    }

    /** Helper for forming a text message from array.
     * @param array $translations
     * @param string $handlingMessage
     * @return void
     */
    private function messageFormate(array $translations, string &$handlingMessage): void
    {
        foreach ($translations as $translation) {
            $handlingMessage .= '- ' . $translation . PHP_EOL;
        }
        $handlingMessage .= PHP_EOL;
    }

    /**
     * @param string $transcription
     * @param bool|array $translations
     * @param array $pronunciationFileNamesAndDirs
     * @param ForeignService $source
     */
    private function translationsPrepareToMessageAndSend(
        string $transcription,
        bool|array $translations,
        array $pronunciationFileNamesAndDirs,
        ForeignService $source
    ): void {
        event(new EnglishWordTranslateEvent(
            $this->englishWordModel,
            $transcription,
            $translations,
            $pronunciationFileNamesAndDirs,
            $source
        ));

        $this->userService->addSentStudyWordId($this->user, $this->englishWordModel->id);

        $transcription = !is_null($this->englishWordModel->transcription)
            ? '  { ' . $this->englishWordModel->transcription . ' }: '
            : ': '
        ;
        $message = $this->englishWordModel->word . $transcription . PHP_EOL;

        $this->messageFormate($translations, $message);
        $ukPronFileNameAndDir = storage_path() . '/app/public' . $this->englishWordModel->uk_pron_file;
        $this->messageCacheService->addToMessageWithFilePathUsingSortedSet(
            $this->parentJobId,
            $this->doseIndex,
            $message,
            $this->englishWordModel->word,
            $ukPronFileNameAndDir
        );
    }
}

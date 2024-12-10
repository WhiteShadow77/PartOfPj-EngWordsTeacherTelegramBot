<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\EnglishWordService;
use App\Services\Cache\MessageCacheService;
use App\Services\RussianWordService;
use App\Services\Cache\StudyWordCacheService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Traits\LoggerTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class StudyWordUserJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use LoggerTrait;

    private string $telegramUserId;
    private bool $withoutPortion;
    private User $userModel;
    private string $cacheIdentifier;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $userModel, string $cacheIdentifier, bool $withoutPortion = false)
    {
        $this->userModel = $userModel;
        $this->telegramUserId = $userModel->telegram_user_id;
        $this->withoutPortion = $withoutPortion;
        $this->cacheIdentifier = $cacheIdentifier;

        $this->writeInfoLog('Study word user job start', [
            'user id' => $userModel->id,
            'telegram user id ' => $this->telegramUserId,
            'without portion' => $withoutPortion,
            'cache identifier' => $cacheIdentifier
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        MessageCacheService $messageCacheService,
        UserService $userService,
        RussianWordService $russianWordService,
        EnglishWordService $englishWordService,
        TelegramService $telegramService,
        StudyWordCacheService $studyWordCacheService
    ): void {
        $parentJobId = $this->job->getJobId();
        $this->writeInfoLog('Study word user job execution', [
            'job id' => $this->job->getJobId(),
            'parent job id' => $parentJobId
        ]);

        $portion = $this->userModel->eng_words_per_twitch;
        $chatId = $this->userModel->chat_id;

        $sentStudyWordsIds = $userService->getSentStudyWordsIds($this->userModel);
        $untranslatedStudyWordsIds = $userService->getUntranslatedStudyWordsIds($this->userModel);
        $handledStudyWordsIds = array_merge($sentStudyWordsIds, $untranslatedStudyWordsIds);

        $this->writeInfoLog('Handled study words ids', [
            'ids' => $handledStudyWordsIds
        ]);

        if (!$this->withoutPortion) {
            $portionOfStudyWordsIds = $userService->getPortionOfStudyWordsIdsExcept(
                $handledStudyWordsIds,
                $this->userModel,
                $portion
            );
            $studyWordsIds = $portionOfStudyWordsIds;
        } else {
            $studyWordsIds = $userService->getStudyWordsIdsExcept(
                $handledStudyWordsIds,
                $this->userModel
            );
        }

        $studyWords = $englishWordService->getCollectionOfEnglishWordsByIds($studyWordsIds);

        $this->writeInfoLog('Study words', [
            'study words' => $studyWords
        ]);

        $chain = [];

        $i = 1;
        while (true) {
            $studyWordItem = $studyWords->current();
            if (!is_null($studyWordItem)) {
                $studyWordCacheService->removeStudyWordIdFromCache($this->cacheIdentifier, $studyWordItem->id);

                $this->writeInfoLog('Study word item from array', [
                    'item' => $studyWordItem
                ]);

                $translations = $englishWordService->translate($studyWordItem->word);
                if ($translations !== false) {
                    if (is_null($translations)) {
                        $this->writeInfoLog('Writing study words to the chain', [
                            'study word' => $studyWordItem->word,
                            'job id' => $this->job->getJobId(),
                            'dose index' => $i
                        ]);

                        $chain[] = new TranslateStudyWordJob(
                            $this->job->getJobId(),
                            $userService,
                            $this->userModel,
                            $i,
                            $studyWordItem,
                            $russianWordService,
                            $englishWordService,
                            $messageCacheService
                        );
                    } else {
                        $this->writeInfoLog(
                            'Study word has translated from DB',
                            [
                            'word' => $studyWordItem->word
                            ],
                            isAllowedSendToTlg: true
                        );

                            $this->writeInfoLog(
                                'Writing translated from DB study words to the chain for preparing telegram message'
                            );
                            $chain[] = new TranslateWordMessageJob(
                                $this->job->getJobId(),
                                $translations,
                                $this->userModel,
                                $studyWordItem,
                                $i
                            );
                    }
                } else {
                    $englishWordService->addNewWord($studyWordItem->word);
                }
            } else {
                if (!$this->withoutPortion) {
                    if ($i == $portion) {
                        break;
                    }
                }
                break;
            }
            $studyWords->next();
            $i++;
        }

        if (sizeof($chain) > 0) {
            $chain[] = function () use ($messageCacheService, $telegramService, $chatId, $parentJobId) {
                $message = $messageCacheService->getMessageWithFilePathUsingSortedSetAndSortByDoseIndex(
                    $parentJobId,
                    $englishWords,
                    $attachmentFileNamesAndDirs
                );
                $telegramService->sendMessage($chatId, $message);
                foreach ($englishWords as $englishWord) {
                    $attachmentFileNameAndDir = current($attachmentFileNamesAndDirs);
                    $telegramService->sendMp3File(
                        $attachmentFileNameAndDir,
                        $chatId,
                        title: $englishWord
                    );
                        next($attachmentFileNamesAndDirs);
                }
                $messageCacheService->deleteMessage($parentJobId);
            };
            Bus::chain($chain)->onQueue('study_words')->dispatch();
        }
    }
}

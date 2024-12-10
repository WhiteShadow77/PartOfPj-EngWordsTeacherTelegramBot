<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Cache\LanguageCacheService;
use App\Services\Cache\MessageCacheService;
use App\Services\Cache\StudyWordCacheService;
use App\Services\EnglishWordService;
use App\Services\History\HistoryMessageService;
use App\Services\StatisticsService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Traits\LoggerTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
use App\Enums\MessageType;

class KnownStudyWordUserJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use LoggerTrait;

    private string $telegramUserId;
    private User $userModel;
    private array $studyWords;
    private string $cacheIdentifier;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $userModel, string $cacheIdentifier, array $studyWords = [])
    {
        $this->userModel = $userModel;
        $this->telegramUserId = $userModel->telegram_user_id;
        $this->studyWords = $studyWords;
        $this->cacheIdentifier = $cacheIdentifier;

        $this->writeInfoLog('Known study word user job start', [
            'user id' => $userModel->id,
            'telegram user id ' => $this->telegramUserId,
            'study words' => $this->studyWords,
            'cache identifier' => $this->cacheIdentifier
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
        EnglishWordService $englishWordService,
        TelegramService $telegramService,
        StudyWordCacheService $studyWordCacheService,
        StatisticsService $statisticsService,
        LanguageCacheService $languageCacheService
    ): void {
        $studyWordsIds = $studyWordCacheService->getStudyWordsIdsFromCacheAndFree($this->cacheIdentifier);

        $parentJobId = $this->job->getJobId();
        $this->writeInfoLog('Known study user job execution', [
            'study words ids from cache' => $studyWordsIds,
            'job id' => $this->job->getJobId(),
            'parent job id' => $parentJobId
        ]);

        $userModel = $this->userModel;

        if (sizeof($studyWordsIds) > 0) {
            $knownWordsIds = $userService->getKnownWordsIds($userModel);

            $knownWordsIds = array_intersect($knownWordsIds, $studyWordsIds);

            $this->writeInfoLog('Got known study words ids by intersection with already known study words ids  and entered study words ids', [
                'entered study words ids' => $studyWordsIds,
                'result' => $knownWordsIds,
            ]);

            $studyWordsIds = array_diff($studyWordsIds, $knownWordsIds);
            $this->writeInfoLog('Got unknown study words ids, differed known words ids from entered study words ids', [
                'known words ids' => $knownWordsIds,
                'result' => $studyWordsIds,
            ]);

            $studyWordCacheService->setMultipleStudyWordIdInCache($this->cacheIdentifier, $studyWordsIds);

            if (sizeof($knownWordsIds) > 0) {
                $userLanguage = $languageCacheService->getLanguageInsteadFromModel($userModel);
                App::setLocale($userLanguage);

                $preMessageText = json_encode([
                    'message' => __('These words have already been studied') . ':' . PHP_EOL . PHP_EOL
                ]);
                $messageCacheService->addToMessageUsingSortedSet($parentJobId, 0, $preMessageText);

                $translations = $englishWordService->translateSeveral($knownWordsIds);
                $chain = [];
                $i = 1;

                /** @var $knownWords */
                $englishWordsModels = $englishWordService->getEnglishWordsModelsCollection($knownWordsIds, $knownWords);

                foreach ($translations as $englishWordId => $translation) {
                    $chain[] = new StudyWordMessageJob(
                        $this->job->getJobId(),
                        $translation,
                        $this->userModel,
                        $englishWordsModels->current(),
                        $i
                    );
                    $i++;
                    $englishWordsModels->next();
                }
                $chain[] = function () use (
                    $messageCacheService,
                    $telegramService,
                    $userModel,
                    $parentJobId,
                    $userService,
                    $knownWordsIds,
                    $knownWords,
                    $i,
                    $englishWordService,
                    $statisticsService,
                    $languageCacheService
                ) {
                    $userService->removeKnownWords($userModel, $knownWordsIds);
                    $statisticsService->removeKnownWordsRecords($userModel, $knownWordsIds);

                    $preMessageText = json_encode([
                        'message' => hex2bin('E29D97') .
                            __("I'll put them back to study") .
                            hex2bin('F09F9895') .
                            PHP_EOL
                    ]);
                    $messageCacheService->addToMessageUsingSortedSet($parentJobId, $i, $preMessageText);

                    $message = $messageCacheService->getMessageWithFilePathUsingSortedSetAndSortByDoseIndex(
                        $parentJobId,
                        $englishWords,
                        $attachmentFileNamesAndDirs
                    );
                    $telegramService->sendMessage($userModel->chat_id, $message);
                    foreach ($englishWords as $englishWord) {
                        $attachmentFileNameAndDir = current($attachmentFileNamesAndDirs);
                        $telegramService->sendMp3File(
                            $attachmentFileNameAndDir,
                            $userModel->chat_id,
                            title: $englishWord
                        );
                        next($attachmentFileNamesAndDirs);
                    }
                    $messageCacheService->deleteMessage($parentJobId);

                    $userLanguage = $languageCacheService->getLanguageInsteadFromDbByChatId($userModel->chat_id);
                    App::setLocale($userLanguage);

                    $historyMessageService = new HistoryMessageService();
                    $historyMessageService->addRecordFromArray(
                        $knownWords,
                        $userModel->id,
                        MessageType::error,
                        __("Deleted already known words") . ': '
                    );
                };
                Bus::chain($chain)->onQueue('study_words')->dispatch();
            }
        }
    }
}

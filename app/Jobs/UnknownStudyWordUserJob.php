<?php

namespace App\Jobs;

use App\Models\EnglishWord;
use App\Models\User;
use App\Services\EnglishWordService;
use App\Services\Cache\MessageCacheService;
use App\Services\Cache\StudyWordCacheService;
use App\Services\TelegramService;
use App\Traits\LoggerTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class UnknownStudyWordUserJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use LoggerTrait;

    private string $telegramUserId;
    private User $userModel;
    private array $studyWords;
    private string $identifier;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $userModel, string $identifier, array $studyWords = [])
    {
        $this->userModel = $userModel;
        $this->telegramUserId = $userModel->telegram_user_id;
        $this->studyWords = $studyWords;
        $this->identifier = $identifier;

        $this->writeInfoLog('Unknown study word user job start', [
            'user id' => $userModel->id,
            'telegram user id ' => $this->telegramUserId,
            'study words' => $this->studyWords,
            'identifier' => $this->identifier
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        MessageCacheService $messageCacheService,
        EnglishWordService $englishWordService,
        TelegramService $telegramService,
        StudyWordCacheService $studyWordCacheService
    ): void {
        $studyWordsIds = $studyWordCacheService->getStudyWordsIdsFromCacheAndFree($this->identifier);

        $parentJobId = $this->job->getJobId();
        $this->writeInfoLog('Unknown study word user job execution', [
            'study words ids from cache' => $studyWordsIds,
            'study words' => $this->studyWords,
            'job id' => $this->job->getJobId(),
            'parent job id' => $parentJobId
        ]);

        $chatId = $this->userModel->chat_id;
        if (sizeof($studyWordsIds) > 0) {
            $translations = $englishWordService->translateSeveral($studyWordsIds);
            if (!is_null($translations) && false !== $translations) {
                $chain = [];
                $i = 1;

                $englishWordsModels = EnglishWord::find($studyWordsIds)->getIterator();

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
}

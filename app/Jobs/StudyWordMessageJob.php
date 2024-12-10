<?php

namespace App\Jobs;

use App\Enums\SentWordsKind;
use App\Models\EnglishWord;
use App\Models\User;
use App\Services\Cache\MessageCacheService;
use App\Traits\LoggerTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StudyWordMessageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use LoggerTrait;

    private int|string $parentJobId;
    private array $translations;
    private User $userModel;
    private EnglishWord $englishWordModel;
    private int $doseIndex;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        int|string $parentJobId,
        array $translations,
        User $userModel,
        EnglishWord $englishWordModel,
        int $doseIndex
    ) {
        $this->parentJobId = $parentJobId;
        $this->translations = $translations;
        $this->userModel = $userModel;
        $this->englishWordModel = $englishWordModel;
        $this->doseIndex = $doseIndex;
        $this->writeInfoLog('Study word message job start', [
            'translations' => $this->translations,
            'user id' => $userModel->id,
            'study word model' => $englishWordModel,
            'dose index' => $doseIndex,
            'parent job id' => $parentJobId
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MessageCacheService $messageCacheService)
    {
        $this->writeInfoLog('Study word message job execution', [
            'job id' => $this->job->getJobId(),
            'translations' => $this->translations,
            'uk pron file' => $this->englishWordModel->uk_pron_file,
            'parent job id' => $this->parentJobId
        ]);

        $transcription = !is_null($this->englishWordModel->transcription)
            ? '  { ' . $this->englishWordModel->transcription . ' }: '
            : ': '
        ;
        $message = $this->englishWordModel->word . $transcription . PHP_EOL;

        $this->messageFormate($this->translations, $message);

        $ukPronFileNameAndDir = storage_path() . '/app/public' . $this->englishWordModel->uk_pron_file;
        $messageCacheService->addToMessageWithFilePathUsingSortedSet(
            $this->parentJobId,
            $this->doseIndex,
            $message,
            $this->englishWordModel->word,
            $ukPronFileNameAndDir
        );
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
}

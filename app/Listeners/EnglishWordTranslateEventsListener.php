<?php

namespace App\Listeners;

use App\Services\EnglishWordService;
use App\Services\RussianWordService;
use App\Traits\LoggerTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EnglishWordTranslateEventsListener
{
    use LoggerTrait;

    private RussianWordService $russianWordService;
    private EnglishWordService $englishWordService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        RussianWordService $russianWordService,
        EnglishWordService $englishWordService,
    ) {
        $this->russianWordService = $russianWordService;
        $this->englishWordService = $englishWordService;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event): void
    {
        $encodedUkSavingDir = $event->source->saveDirMediaTypeFolderEncode($event->pronunciationFileNamesAndDirs[0]);
        $encodedUsSavingDir = $event->source->saveDirMediaTypeFolderEncode($event->pronunciationFileNamesAndDirs[1]);

        $this->writeInfoLog(
            'Translated english word',
            [
                'english word id' => $event->englishWordModel?->id,
                'english word' => $event->englishWordModel?->word ?? $event->englishWord,
                'transcription' => $event->transcription,
                'russian translate' => $event->translations,
                'uk pron. saving file name and dir' => $encodedUkSavingDir,
                'us pron. saving file name and dir' => $encodedUsSavingDir,
            ]
        );
        $event->source->downloadSeveralMp3FileAndSave($event->pronunciationFileNamesAndDirs);
        if (!is_null($event->englishWordModel)) {
            $this->englishWordService->addTranscriptionAndPronFileNamesAndDirs(
                $event->englishWordModel,
                $event->transcription,
                $encodedUkSavingDir,
                $encodedUsSavingDir
            );
            $this->russianWordService->assignWordsByEnglishWordModel($event->englishWordModel, $event->translations);
        } else {
            $englishWordModel = $this->englishWordService->addEnglishWordTranscriptionPronFileNamesAndDirs(
                $event->englishWord,
                $event->transcription,
                $encodedUkSavingDir,
                $encodedUsSavingDir
            );
            $this->russianWordService->assignWordsByEnglishWordModel($englishWordModel, $event->translations);
        }
    }
}

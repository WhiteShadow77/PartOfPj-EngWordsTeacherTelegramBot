<?php

namespace App\Listeners;

use App\Models\StudyWord;
use App\Services\RussianWordService;
use App\Services\StudyWordsService;
use App\Traits\LoggerTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StudyWordTranslateEventsListener
{
    use LoggerTrait;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(private StudyWordsService $studyWordsService)
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeInfoLog(
            'Translated study word',
            [
                'study word' => $event->studyWordModel->word,
                'study word id' => $event->studyWordModel->id
            ]
        );
        $this->studyWordsService->assignWithEnglishWord($event->studyWordModel->word, $event->studyWordModel->id);
    }
}

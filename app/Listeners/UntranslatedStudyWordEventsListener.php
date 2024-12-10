<?php

namespace App\Listeners;

use App\Services\UntranslatedStudyWordService;
use App\Services\UntranslatedWordService;
use App\Traits\LoggerTrait;

class UntranslatedStudyWordEventsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        private UntranslatedStudyWordService $untranslatedStudyWordService,
        private UntranslatedWordService $untranslatedWordService
    ) {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event): void
    {
        $this->untranslatedStudyWordService->insertUntranslatedWordAndAttachToUser($event->userModel, $event->englishWord->word);
        $this->untranslatedWordService->insertUntranslatedWordWithWord($event->englishWord->id, $event->englishWord->word);
    }
}

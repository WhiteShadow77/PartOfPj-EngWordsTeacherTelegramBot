<?php

namespace App\Listeners;

use App\Events\UntranslatedWordEvent;
use App\Models\UntranslatedWordId;
use App\Services\UntranslatedWordService;
use App\Traits\LoggerTrait;

class UntranslatedWordEventsListener
{
    use LoggerTrait;

    public function __construct(private UntranslatedWordService $untranslatedWordService)
    {
    }

    public function handle(UntranslatedWordEvent $event): void
    {
        $this->untranslatedWordService->insertUntranslatedWordWithWord($event->engWordId, $event->word);
    }
}

<?php

namespace App\Listeners;

use App\Services\UntranslatedWordService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UntranslatedWordByIdEventsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(private UntranslatedWordService $untranslatedWordService)
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->untranslatedWordService->insertUntranslatedWord($event->englishWordId);
    }
}

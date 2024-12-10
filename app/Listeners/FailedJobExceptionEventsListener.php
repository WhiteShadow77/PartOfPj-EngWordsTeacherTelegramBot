<?php

namespace App\Listeners;

use App\Services\RussianWordService;
use App\Traits\LoggerTrait;
use Exception;

class FailedJobExceptionEventsListener
{
    use LoggerTrait;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event): void
    {
        $this->writeErrorLog('Failed job exception', [
            'message' => $event->exception->getMessage(),
            'file' => $event->exception->getfile(),
            'line' => $event->exception->getline(),
            'code' => $event->exception->getcode(),
        ]);
    }
}

<?php

namespace App\Services\Commands;

use App\Models\FailedJob;
use App\Traits\LoggerTrait;

class DeleteDBFailedJobsCommandService
{
    use LoggerTrait;

    public function __invoke(): void
    {
        FailedJob::truncate();

        $this->writeInfoLog('All failed jobs has been deleted from DB', isAllowedSendToTlg: true);
    }
}

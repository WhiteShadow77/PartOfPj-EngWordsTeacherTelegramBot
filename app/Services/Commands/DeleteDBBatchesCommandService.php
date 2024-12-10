<?php

namespace App\Services\Commands;

use App\Models\Job_Batch;
use App\Traits\LoggerTrait;

class DeleteDBBatchesCommandService
{
    use LoggerTrait;

    public function __invoke(): void
    {
        Job_Batch::truncate();

        $this->writeInfoLog('All job batches has been deleted from DB', isAllowedSendToTlg: true);
    }
}

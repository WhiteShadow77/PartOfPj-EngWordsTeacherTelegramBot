<?php

namespace App\Services\Commands;

use App\Jobs\DistributionJob;
use App\Traits\LoggerTrait;

class DistributionCommandService
{
    use LoggerTrait;

    public function __invoke(): void
    {
        $this->writeInfoLog('Command distribution is running');

        DistributionJob::dispatch(date('H:i'))->onQueue('test');
    }
}

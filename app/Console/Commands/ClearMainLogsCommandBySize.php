<?php

namespace App\Console\Commands;

use App\Services\Commands\ClearMainLogsCommandBySizeService;
use App\Traits\LoggerTrait;
use Illuminate\Console\Command;

class ClearMainLogsCommandBySize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear-main-by-size {size : lof file size in Bytes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the main log file, by pointed file size in Bytes.';

    /**
     * Execute the console command.
     *
     * @param ClearMainLogsCommandBySizeService $clearMainLogsCommandBySizeService
     * @return int
     */
    public function handle(ClearMainLogsCommandBySizeService $clearMainLogsCommandBySizeService)
    {
        $clearMainLogsCommandBySizeService($this->argument('size'));

        return Command::SUCCESS;
    }
}

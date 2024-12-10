<?php

namespace App\Console\Commands;

use App\Services\Commands\SaveLogsCopyCommandService;
use Illuminate\Console\Command;

class SaveLogsCopyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:save-copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes copy of log and saves.';

    /**
     * Execute the console command.
     *
     * @param SaveLogsCopyCommandService $saveLogsCopyCommandService
     * @return int
     */
    public function handle(SaveLogsCopyCommandService $saveLogsCopyCommandService): int
    {
        $saveLogsCopyCommandService();

        return Command::SUCCESS;
    }
}

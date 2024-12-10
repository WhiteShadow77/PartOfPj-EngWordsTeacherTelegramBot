<?php

namespace App\Console\Commands;

use App\Services\Commands\DeleteDBFailedJobsCommandService;
use App\Traits\LoggerTrait;
use Illuminate\Console\Command;

class DeleteDBFailedJobsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:db-failed-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all failed jobs from database';

    /**
     * Execute the console command.
     *
     * @param DeleteDBFailedJobsCommandService $deleteDBFailedJobsCommandService
     * @return int
     */
    public function handle(DeleteDBFailedJobsCommandService $deleteDBFailedJobsCommandService)
    {
        $deleteDBFailedJobsCommandService();

        return Command::SUCCESS;
    }
}

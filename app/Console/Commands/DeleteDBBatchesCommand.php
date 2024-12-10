<?php

namespace App\Console\Commands;

use App\Services\Commands\DeleteDBBatchesCommandService;
use App\Traits\LoggerTrait;
use Illuminate\Console\Command;

class DeleteDBBatchesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:db-batches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all job batches from database';

    /**
     * Execute the console command.
     *
     * @param DeleteDBBatchesCommandService $deleteDBBatchesCommandService
     * @return int
     */
    public function handle(DeleteDBBatchesCommandService $deleteDBBatchesCommandService)
    {
        $deleteDBBatchesCommandService();

        return Command::SUCCESS;
    }
}

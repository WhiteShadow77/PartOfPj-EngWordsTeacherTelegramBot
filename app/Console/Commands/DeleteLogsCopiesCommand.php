<?php

namespace App\Console\Commands;

use App\Services\CodeExplorerService;
use App\Services\Commands\DeleteLogsCopiesCommandService;
use App\Traits\LoggerTrait;
use Illuminate\Console\Command;

class DeleteLogsCopiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:delete-copies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes copies of logs';

    /**
     * Execute the console command.
     *
     * @param CodeExplorerService $codeExplorerService
     * @param DeleteLogsCopiesCommandService $deleteLogsCopiesCommandService
     * @return int
     */
    public function handle(
        CodeExplorerService $codeExplorerService,
        DeleteLogsCopiesCommandService $deleteLogsCopiesCommandService
    ) {
        $deleteLogsCopiesCommandService($codeExplorerService);

        return Command::SUCCESS;
    }
}

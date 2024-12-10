<?php

namespace App\Console\Commands;

use App\Services\Commands\DistributionCommandService;
use Illuminate\Console\Command;

/** Command that runs english words and quizes distribution of all users */
class DistributionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'distribution';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that runs english words and quizes distribution of all users';

    /**
     * Execute the console command.
     *
     * @param DistributionCommandService $distributionCommandService
     * @return int
     */
    public function handle(DistributionCommandService $distributionCommandService)
    {
        $distributionCommandService();

        return Command::SUCCESS;
    }
}

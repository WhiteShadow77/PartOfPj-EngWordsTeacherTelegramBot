<?php

namespace App\Console\Commands;

use App\Exceptions\TranslateRemoteServiceException;
use App\Services\Commands\DownloadPronFilesCommandService;
use App\Services\EnglishWordService;
use App\Services\ForeignService;
use Illuminate\Console\Command;

class DownloadPronFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:pron {word_count : count of words to make download for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloads pron files to 10 english words in db without it';

    /**
     * Execute the console command.
     *
     * @param ForeignService $foreignService
     * @param EnglishWordService $englishWordService
     * @param DownloadPronFilesCommandService $downloadPronFilesCommandService
     * @return int
     * @throws TranslateRemoteServiceException
     */
    public function handle(
        ForeignService $foreignService,
        EnglishWordService $englishWordService,
        DownloadPronFilesCommandService $downloadPronFilesCommandService
    ): int {
        $downloadPronFilesCommandService($foreignService, $englishWordService, $downloadPronFilesCommandService);

        return Command::SUCCESS;
    }
}

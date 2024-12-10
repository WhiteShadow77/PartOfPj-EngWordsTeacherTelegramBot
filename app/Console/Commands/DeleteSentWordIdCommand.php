<?php

namespace App\Console\Commands;

use App\Enums\SentWordsKind;
use App\Services\Commands\DeleteSentWordIdCommandService;
use App\Services\UserService;
use Illuminate\Console\Command;

class DeleteSentWordIdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:sent-word-id {id : id of the word} {user_id : id of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes sent word id of the user';

    /**
     * Execute the console command.
     *
     * @param UserService $userService
     * @param DeleteSentWordIdCommandService $deleteSentWordIdCommandService
     * @return int
     */
    public function handle(UserService $userService, DeleteSentWordIdCommandService $deleteSentWordIdCommandService)
    {
        $deleteSentWordIdCommandService($userService);

        return Command::SUCCESS;
    }
}

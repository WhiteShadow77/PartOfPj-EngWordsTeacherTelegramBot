<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Cache\LanguageCacheService;
use App\Services\EnglishWordService;
use App\Services\History\HistoryMessageService;
use App\Services\Cache\MessageCacheService;
use App\Services\RussianWordService;
use App\Services\TelegramService;
use App\Services\UserService;
use App\Traits\LoggerTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
use App\Enums\MessageType;

class TwitchUserJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use LoggerTrait;

    private User $userModel;
    private ?string $afterExecutionSendMessage = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $userModel, ?string $afterExecutionSendMessage = null)
    {
        $this->writeInfoLog('Twitch user job start', [
            'id of user' => $userModel->id
        ]);
        $this->userModel = $userModel;
        $this->afterExecutionSendMessage = $afterExecutionSendMessage;
    }

    public function handle(
        MessageCacheService $messageCacheService,
        UserService $userService,
        TelegramService $telegramService,
        RussianWordService $russianWordService,
        EnglishWordService $englishWordService,
        HistoryMessageService $historyMessageService,
        LanguageCacheService $languageCacheService
    ) {
        $chatId = $this->userModel->chat_id;
        $parentJobId = $this->job->getJobId();

        $this->writeInfoLog('Twitch user job execution', [
            'id of user' => $this->userModel->id,
            'name' => $this->job->getName(),
            'job id' => $this->job->getJobId(),
            'parent job id' => $this->job->getJobId()
        ]);
        for ($i = 1; $i <= $this->userModel->eng_words_per_twitch; $i++) {
            $chain[] = new TranslateWordJob(
                $parentJobId,
                $userService,
                $this->userModel,
                $i,
                $russianWordService,
                $englishWordService,
                $messageCacheService
            );
        }
        $userModel = $this->userModel;
        $afterExecutionSendMessage = $this->afterExecutionSendMessage;

        $chain[] = function () use (
            $messageCacheService,
            $telegramService,
            $chatId,
            $parentJobId,
            $historyMessageService,
            $userModel,
            $afterExecutionSendMessage,
            $languageCacheService
        ) {
            $message = $messageCacheService->getMessageWithFilePathUsingSortedSetAndSortByDoseIndex(
                $parentJobId,
                $englishWords,
                $filesAttachmentResult
            );
            $telegramService->sendMessage($chatId, $message);

            foreach ($filesAttachmentResult as $filesAttachmentResultItem) {
                $currentEnglishWord = current($englishWords);
                $telegramService->sendMp3File(
                    $filesAttachmentResultItem,
                    $chatId,
                    title: $currentEnglishWord
                );
                next($englishWords);
            }

            $userLanguage = $languageCacheService->getLanguageInsteadFromModel($userModel);
            App::setLocale($userLanguage);

            $messageCacheService->deleteMessage($parentJobId);
            $historyMessageService->addRecord(
                __("Received words") . ':',
                $userModel->id,
                MessageType::info,
                $englishWords
            );

            if (!is_null($afterExecutionSendMessage)) {
                $telegramService->sendMessage($chatId, $afterExecutionSendMessage);
            }
        };
        Bus::chain($chain)->onQueue('twitch')->dispatch();
    }
}

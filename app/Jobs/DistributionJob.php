<?php

namespace App\Jobs;

use App\Services\Cache\LanguageCacheService;
use App\Services\Helpers\FieldId;
use App\Services\TelegramService;
use App\Traits\LoggerTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\App;

class DistributionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use LoggerTrait;

    private string $currentTime;

    /**
     * Create a new job instance.
     *
     * @param string $currentTime
     * @return void
     */
    public function __construct(string $currentTime)
    {
        $this->currentTime = $currentTime;
        $this->writeInfoLog('Distribution job start', [
            'current time' => $this->currentTime
        ]);
    }

    /**
     * Execute the job.
     *
     * @param TelegramService $telegramService
     * @param LanguageCacheService $languageCacheService
     * @return void
     */
    public function handle(TelegramService $telegramService, LanguageCacheService $languageCacheService)
    {
        $weekDayNumber = date('w');
        $weekDaysNumberAndCodeBindings = config('english_words.week_days_number_and_code_binding');

        $this->writeInfoLog('Distribution job is running', [
            'week days number and code bindings' => $weekDaysNumberAndCodeBindings,
            'week day number' => $weekDayNumber,
            'job id' => $this->job->getJobId()
        ]);

        $weekDayCode = $weekDaysNumberAndCodeBindings[$weekDayNumber];

        $this->writeInfoLog('Distribution job is running', [
            'week day code' => $weekDayCode,
            'job id' => $this->job->getJobId()
        ]);

        $usersToSendEngWords = User::where('english_words_week_sending_conf', '&', $weekDayCode)
            ->where('is_enabled_english_words_sending', 1)->get();
        $usersToSendQuiz = User::where('quiz_week_sending_conf', '&', $weekDayCode)
            ->where('is_enabled_quiz_sending', 1)->get();

        if (sizeof($usersToSendEngWords) > 0) {
            foreach ($usersToSendEngWords as $userToSendEngWords) {
                $sendingDaysTimesBindings = json_decode(
                    $userToSendEngWords->english_words_week_and_times_sending_conf,
                    true
                );
                $sendingTime = $sendingDaysTimesBindings[$weekDayCode] ?? null;

                if (!is_null($sendingTime) && ($this->currentTime == $sendingTime)) {
                    $this->writeInfoLog('Executing send eng words');
                    TwitchUserJob::dispatch($userToSendEngWords)->onQueue('twitch');
                } else {
                    $this->writeErrorLog(
                        'Warning:  Sending time is null or current time is not equal to sending time',
                        [
                            'sending time' => $sendingTime,
                            'current time' => $this->currentTime
                        ]
                    );
                }
            }
        } else {
            $this->writeInfoLog('No users to send english words', [
                'day number of week today' => $weekDayNumber
            ]);
        }

        if (sizeof($usersToSendQuiz) > 0) {
            foreach ($usersToSendQuiz as $userToSendQuiz) {
                $sendingDaysTimesBindings = json_decode($userToSendQuiz->quiz_week_and_times_sending_conf, true);
                $quizQuantitiesBindings = json_decode($userToSendQuiz->quiz_quantity_sending_conf, true);
                $sendingTime = $sendingDaysTimesBindings[$weekDayCode] ?? null;
                $quizQuantity = $quizQuantitiesBindings[$weekDayCode] ?? null;

                if (!is_null($sendingTime) && !is_null($quizQuantity) && ($this->currentTime == $sendingTime)) {
                    $userId = $userToSendQuiz->id;

                    $userLanguage = $languageCacheService->getLanguageInsteadFromModel($userToSendQuiz);
                    App::setLocale($userLanguage);

                    $time = time();
                    $buttons = [
                        [
                            [
                                'text' => __('Yes'),
                                'callback_data' => '#quiz start ' . $userId . ' ' . $quizQuantity . ' ' . $time,
                            ],
                            [
                                'text' => __('No'),
                                'callback_data' => '#quiz discard ' . $userId . ' ' . $time,
                            ]
                        ]
                    ];

                    $this->writeInfoLog('Asking about a quiz');

                    $telegramService->sendMessageAndButtons(
                        $userToSendQuiz->chat_id,
                        __("It's time to test your knowledge. I'll run a test. Response time") . ' ' .
                        config('quiz.user_answer_waiting_seconds') . ' ' .
                        __("seconds Shall we begin?"),
                        $buttons
                    );

                    $cacheFieldId = FieldId::make($userId, $time);
                    $telegramService->setButtonsStructToCache($userId, $cacheFieldId, $buttons);
                } else {
                    $this->writeErrorLog(
                        'Warning: Sending time is null or current time is not equal to sending time or wrong quiz quantity',
                        [
                            'sending time' => $sendingTime,
                            'current time' => $this->currentTime,
                            'quiz quantity' => $quizQuantity
                        ]
                    );
                }
            }
        } else {
            $this->writeInfoLog('No users to send quiz', [
                'day number of week today' => $weekDayNumber
            ]);
        }
    }
}

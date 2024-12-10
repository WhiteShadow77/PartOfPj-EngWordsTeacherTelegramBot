<?php

namespace App\Services\Controller;

use App\Models\User;
use App\Services\Cache\LanguageCacheService;
use App\Services\History\HistoryMessageService;
use App\Services\ResponseService;
use App\Services\SendSchedulesService;
use App\Traits\LoggerTrait;
use App\Enums\SendScheduleKind;
use App\Enums\MessageType;
use Illuminate\Support\Facades\App;

class UserControllerService
{
    use LoggerTrait;

    private ResponseService $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function updateEnglishWordsPortion(int $portion, int $userId)
    {
        $this->writeInfoLog('Updating english word portion', [
            'user id' => $userId,
            'portion' => $portion
        ]);

        User::where('id', $userId)->update(['eng_words_per_twitch' => $portion]);
        $this->writeInfoLog('Successfully updated english word portion');

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'portion' => $portion
            ]
        ], 'Updated english word portion');
    }

    public function updateQuizMaxAnswersQuantity(int $quantity, int $userId)
    {
        User::where('id', $userId)->update(['quiz_max_answers' => $quantity]);

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'quantity' => $quantity
            ]
        ], 'Updated max quiz quantity');
    }

    public function getEnglishWordsIsEnabled(int $userId)
    {
        $userModel = User::where('id', $userId)->first();

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'is_enabled' => $userModel->is_enabled_english_words_sending
            ]
        ], 'Is enabled english words sending');
    }

    public function getQuizIsEnabled(int $userId)
    {
        $userModel = User::where('id', $userId)->first();

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'is_enabled' => $userModel->is_enabled_quiz_sending
            ]
        ], 'Is enabled quiz sending');
    }

    public function getQuizSendingSchedule(SendSchedulesService $sendScheduleService)
    {
        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'schedule' => $sendScheduleService->getSchedule(SendScheduleKind::quiz)
            ]
        ], 'Quiz sending schedule');
    }

    public function getEngWordsSendingSchedule(SendSchedulesService $sendScheduleService)
    {
        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'schedule' => $sendScheduleService->getSchedule(SendScheduleKind::english_words)
            ]
        ], 'English words sending schedule');
    }

    public function updateIsEnabledQuizSending(string $isEnabled, int $userId)
    {
        $this->writeInfoLog('Updating quiz enable sending', [
            'user id' => $userId,
            'is_enabled' => $isEnabled
        ]);

        if ($isEnabled == 'true') {
            $isEnabled = 1;
        } else {
            $isEnabled = 0;
        }

        User::where('id', $userId)->update(['is_enabled_quiz_sending' => $isEnabled]);
        $this->writeInfoLog('Successfully updated quiz enable sending', [
            'is_enabled' => $isEnabled
        ]);

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'is_enabled' => $isEnabled
            ]
        ], 'Updated quiz enable sending');
    }

    public function updateIsEnabledEngWordsSending(string $isEnabled, int $userId)
    {
        $this->writeInfoLog('Updating english words isEnabledSending field', [
            'user id' => $userId,
            'is_enabled' => $isEnabled
        ]);

        if ($isEnabled == 'true') {
            $isEnabled = 1;
        } else {
            $isEnabled = 0;
        }

        User::where('id', $userId)->update(['is_enabled_english_words_sending' => $isEnabled]);
        $this->writeInfoLog('Successfully updated english words isEnabledSending field', [
            'is_enabled' => $isEnabled
        ]);

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'is_enabled' => $isEnabled
            ]
        ], 'Updated english words isEnabledSending field');
    }

    public function updateQuizSendingSchedule(
        ?array $days,
        ?array $times,
        SendScheduleKind $sendScheduleKind,
        ?array $quiz_quantities,
        SendSchedulesService $sendScheduleService
    ) {
        $weekScheduleInstatnce = $sendScheduleService->updateSchedule(
            $days,
            $times,
            SendScheduleKind::quiz,
            $quiz_quantities
        );

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'schedule' => [
                    'days_and_times' => $weekScheduleInstatnce ?->getWeekScheduleWithTimes(),
                    'days_and_quiz_quantity' => $weekScheduleInstatnce ?->getWeekScheduleWithQuizQuantities()
                ]
            ]], 'Updated quiz sending schedule');
    }

    public function getQuizIsEnabledRepeatAlreadyKnown(int $userId)
    {
        $userModel = User::where('id', $userId)->first();

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'is_enabled' => $userModel->is_enabled_repeat_already_known_in_quiz
            ]
        ], 'Is enabled repeat already known in quiz');
    }

    public function updateIsEnabledRepeatKnownInQuiz(string $isEnabled, int $userId)
    {
        $this->writeInfoLog('Updating the is enabled repeat known in quiz', [
            'user id' => $userId,
            'is_enabled' => $isEnabled
        ]);

        if ($isEnabled == 'true') {
            $isEnabled = 1;
        } else {
            $isEnabled = 0;
        }

        User::where('id', $userId)->update(['is_enabled_repeat_already_known_in_quiz' => $isEnabled]);
        $this->writeInfoLog('Successfully updated the is enabled repeat known in quiz', [
            'is_enabled' => $isEnabled
        ]);

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'is_enabled' => $isEnabled
            ]
        ], 'Updated the is enabled repeat known in quiz');
    }

    public function getQuizRepeatAlreadyKnownPercents($userId)
    {
        $this->writeInfoLog('Getting the repeat known words percent in quiz', [
            'user id' => $userId
        ]);

        $percents = User::where('id', $userId)->first()->repeat_known_words_percent_in_quiz;
        $this->writeInfoLog('Successfully got the repeat known words percent in quiz', [
            'percents' => $percents
        ]);

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'percents' => $percents
            ]
        ], 'The repeat known words percents in quiz');
    }

    public function updateRepeatKnownWordsPercentsInQuiz(int $percents, $userId)
    {
        $this->writeInfoLog('Updating the repeat known words percent in quiz', [
            'user id' => $userId,
            'percents' => $percents
        ]);

        User::where('id', $userId)->update(['repeat_known_words_percent_in_quiz' => $percents]);
        $this->writeInfoLog('Successfully updated the repeat known words percents in quiz', [
            'percents' => $percents
        ]);

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'percents' => $percents
            ]
        ], 'Updated the repeat known words percents in quiz');
    }

    public function updateLanguage(
        string $language,
        int $userId,
        LanguageCacheService $languageCacheService,
        HistoryMessageService $historyMessageService
    ) {
        $this->writeInfoLog('Updating users\' language', [
            'user id' => $userId,
            'percents' => $language
        ]);

        $userModel = User::find($userId);
        $prevLanguage = $userModel->language;

        $userModel->update(['language' => $language]);

        $this->writeInfoLog('Successfully updated the repeat known words percents in quiz', [
            'old language' => $prevLanguage,
            'new language' => $language
        ]);

        App::setLocale($language);
        $text = __("Language has changed to") . ' ' . $language;
        $historyMessageService->addRecord($text, $userModel->id, MessageType::info);

        $languageCacheService->setNewLanguage($userModel, $language);

        return $this->responseService->successResponseWithKeyValueData([
            'data' => [
                'language' => $language
            ]
        ], 'Updated user\'s language');
    }
}

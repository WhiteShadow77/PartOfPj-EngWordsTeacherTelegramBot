<?php

namespace App\Http\Controllers\Api;

use App\Enums\SendScheduleKind;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateEngWordsSendingScheduleRequest;
use App\Http\Requests\UpdateIsEnabledEngWordsSendingRequest;
use App\Http\Requests\UpdateIsEnabledQuizSendingRequest;
use App\Http\Requests\UpdateIsEnabledRepeatKnownInQuizRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Http\Requests\UpdateQuizMaxAnswersQuantityRequest;
use App\Http\Requests\UpdateQuizSendingScheduleRequest;
use App\Http\Requests\UpdateRepeatKnownWordsPercentsInQuizRequest;
use App\Services\Cache\LanguageCacheService;
use App\Services\History\HistoryMessageService;
use App\Services\History\HistoryRecordService;
use App\Services\SendSchedulesService;
use App\Services\StatisticsService;
use App\Services\Controller\UserControllerService;
use App\Traits\LoggerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Enums\WordStatus;
use App\Http\Requests\UpdateEnglishWordsPortionRequest;

class UserController extends Controller
{
    use LoggerTrait;

    private UserControllerService $userControllerService;

    public function __construct(UserControllerService $userControllerService)
    {
        $this->userControllerService = $userControllerService;
    }

    public function updateEnglishWordsPortion(UpdateEnglishWordsPortionRequest $request): JsonResponse
    {
        $userId = Auth::user()->id;
        $portion = $request->portion;

        return $this->userControllerService->updateEnglishWordsPortion($portion, $userId);
    }

    public function updateIsEnabledEngWordsSending(UpdateIsEnabledEngWordsSendingRequest $request): JsonResponse
    {
        $userId = Auth::user()->id;
        $isEnabled = $request->is_enabled;

        return $this->userControllerService->updateIsEnabledEngWordsSending($isEnabled, $userId);
    }

    public function updateEngWordsSendingSchedule(
        UpdateEngWordsSendingScheduleRequest $request,
        SendSchedulesService $sendScheduleService
    ): JsonResponse {
        return Response::json([
            'Status' => 'success',
            'message' => 'Updated english words sending schedule',
            'data' => [
                'schedule' => $sendScheduleService->updateSchedule(
                    $request->days,
                    $request->times,
                    SendScheduleKind::english_words
                )
            ]
        ]);
    }

    public function updateQuizSendingSchedule(
        UpdateQuizSendingScheduleRequest $request,
        SendSchedulesService $sendScheduleService
    ): JsonResponse {
        return $this->userControllerService->updateQuizSendingSchedule(
            $request->days,
            $request->times,
            SendScheduleKind::quiz,
            $request->quiz_quantities,
            $sendScheduleService
        );
    }

    public function loginUser(Request $request)
    {
        if (
            !Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            ])
        ) {
            return Response::json(['message' => 'not logged in']);
        } else {
            return Response::json(['message' => 'logged in']);
        }
    }

    public function updateIsEnabledQuizSending(UpdateIsEnabledQuizSendingRequest $request): JsonResponse
    {
        $userId = Auth::id();
        return $this->userControllerService->updateIsEnabledQuizSending($request->is_enabled, $userId);
    }

    public function getEngWordsSendingSchedule(SendSchedulesService $sendScheduleService): JsonResponse
    {
        return $this->userControllerService->getEngWordsSendingSchedule($sendScheduleService);
    }

    public function getQuizSendingSchedule(SendSchedulesService $sendScheduleService): JsonResponse
    {
        return $this->userControllerService->getQuizSendingSchedule($sendScheduleService);
    }

    public function updateQuizMaxAnswersQuantity(UpdateQuizMaxAnswersQuantityRequest $request)
    {
        $userId = Auth::user()->id;
        return $this->userControllerService->updateQuizMaxAnswersQuantity($request->quantity, $userId);
    }

    public function getEnglishWordsIsEnabled()
    {
        $userId = Auth::id();
        return $this->userControllerService->getEnglishWordsIsEnabled($userId);
    }

    public function getQuizIsEnabled()
    {
        $userId = Auth::id();
        return $this->userControllerService->getQuizIsEnabled($userId);
    }

    public function getQuizIsEnabledRepeatAlreadyKnown()
    {
        $userId = Auth::id();
        return $this->userControllerService->getQuizIsEnabledRepeatAlreadyKnown($userId);
    }

    public function updateIsEnabledRepeatKnownInQuiz(UpdateIsEnabledRepeatKnownInQuizRequest $request): JsonResponse
    {
        $userId = Auth::id();
        return $this->userControllerService->updateIsEnabledRepeatKnownInQuiz($request->is_enabled, $userId);
    }

    public function getStatistics(StatisticsService $statisticsService)
    {
        $userId = Auth::id();
        $statisticsKnownWords = $statisticsService->getWordsCountPerDay($userId, WordStatus::known);
        $statisticsUnKnownWords = $statisticsService->getWordsCountPerDay($userId, WordStatus::unknown);

        $statisticsKnownWords->merge($statisticsKnownWords, $statisticsUnKnownWords);

        $this->writeInfoLog('Got statistics of user', [
            'user id' => $userId,
            'result' => $statisticsKnownWords->getDebugInfo()
        ]);

        return Response::json([
            'Status' => 'success',
            'message' => 'User statistics of known and unknown words per day',
            'data' => [
                'dates' => $statisticsKnownWords->getDates(),
                'known_words_count' => $statisticsKnownWords->getKnownWordsCounts(),
                'unknown_words_count' => $statisticsKnownWords->getUnknownWordsCounts()
            ]
        ]);
    }

    public function getHistoryDeletePeriodParams(HistoryRecordService $historyRecordService, int $monthsQuantity)
    {
        $userId = Auth::id();
        $result = $historyRecordService->getDeletePeriodParams($userId, $monthsQuantity);
        return Response::json([
            'Status' => 'success',
            'message' => 'Delete history period params',
            'data' => $result
        ]);
    }

    public function getQuizRepeatAlreadyKnownPercents()
    {
        $userId = Auth::id();
        return $this->userControllerService->getQuizRepeatAlreadyKnownPercents($userId);
    }

    public function updateRepeatKnownWordsPercentsInQuiz(UpdateRepeatKnownWordsPercentsInQuizRequest $request)
    {
        $userId = Auth::id();
        return $this->userControllerService->updateRepeatKnownWordsPercentsInQuiz($request->percents, $userId);
    }

    public function updateLanguage(
        UpdateLanguageRequest $request,
        LanguageCacheService $languageCacheService,
        HistoryMessageService $historyMessageService
    ) {
        $userId = Auth::user()->id;
        return $this->userControllerService
            ->updateLanguage($request->language, $userId, $languageCacheService, $historyMessageService);
    }
}

<?php

namespace App\Services;

use App\Enums\SendScheduleKind;
use App\Services\DataStructures\EnglishWordsSchedule\DayTimesSchedule;
use App\Services\DataStructures\EnglishWordsSchedule\WeekSchedule;
use App\Services\DataStructures\EnglishWordsSchedule\DayQuizQuantitySchedule;
use App\Traits\LoggerTrait;
use Illuminate\Support\Facades\Auth;

class SendSchedulesService
{
    use LoggerTrait;

    public function __construct(
        protected WeekSchedule $weekSchedule,
        protected DayTimesSchedule $dayTimesSchedule,
        protected DayQuizQuantitySchedule $dayQuizQuantitySchedule
    ) {
    }

    public function updateSchedule(
        ?array $days,
        ?array $times,
        SendScheduleKind $sendScheduleKind,
        ?array $quizQuantities = null
    ): ?WeekSchedule {
        $this->writeInfoLog('Updating sending schedule for user', [
            'kind' => $sendScheduleKind,
            'days' => $days,
            'times' => $times,
            'quiz quantities' => $quizQuantities
        ]);

        $this->dayTimesSchedule->setScheduleKind($sendScheduleKind);
        $userId = Auth::user()->id;

        if (!is_null($days)) {
            foreach ($days as $day) {
                $dayTimesScheduleInstance = $this->dayTimesSchedule->setSendingTime(current($times));

                if (!is_null($quizQuantities)) {
                    $dayQuizQuantitiesScheduleInstance = $this->dayQuizQuantitySchedule
                        ->setQuantity(current($quizQuantities));
                    $this->weekSchedule->setSendingDayTimeAndDayQuizQuantity(
                        $day,
                        $dayTimesScheduleInstance,
                        $dayQuizQuantitiesScheduleInstance
                    );
                    next($quizQuantities);
                }

                $this->weekSchedule->setSendingDayTimeAndDayQuizQuantity(
                    $day,
                    $dayTimesScheduleInstance
                );
                next($times);
            }

            $this->writeInfoLog('Week schedule in int handled', [
                'week schedule in int ' => $this->weekSchedule->getWeekSchedule()
            ]);

            return $this->weekSchedule->saveToUser($userId, $sendScheduleKind);
        } else {
            return $this->weekSchedule->disableScheduleAndSaveToUser($userId, $sendScheduleKind);
        }
    }

    public function getSchedule(SendScheduleKind $sendScheduleKind): array
    {
        $userId = Auth::user()->id;
        $this->writeInfoLog('Getting english words sending schedule', [
            'kind' => $sendScheduleKind->name,
            'user id' => $userId
        ]);
        return $this->weekSchedule->getFromUserForFrontEnd($userId, $sendScheduleKind);
    }
}

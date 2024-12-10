<?php

namespace App\Services\DataStructures\EnglishWordsSchedule;

use App\Enums\SendScheduleKind;
use App\Models\User;
use App\Traits\LoggerTrait;

class WeekSchedule
{
    use LoggerTrait;

    protected int $weekSchedule = 0;
    protected array $weekTemplate;
    protected array $weekScheduleWithTimes;
    protected array $weekScheduleWithQuizQuantities = [];

    public function __construct()
    {
        $this->weekTemplate = config('english_words.schedule_sending_days_template');
    }

    public function setSendingDayTimeAndDayQuizQuantity(
        string $dayName,
        DayTimesSchedule $sendingTime,
        ?DayQuizQuantitySchedule $dayQuizQuantitySchedule = null
    ): void {
        if (!in_array($dayName, array_keys($this->weekTemplate))) {
            throw new \Exception('Did not set sending day. Day doesn\'t match the template');
        }

        $this->weekSchedule |= $this->weekTemplate[$dayName];

        $this->writeInfoLog('setSendingDayTimeAndDayQuizQuantity method executing', [
            'day name' => $dayName,
            'week schedule' => $this->weekSchedule,
            'day from week template' => $this->weekTemplate[$dayName],
            'day of quiz quantity schedule' => $dayQuizQuantitySchedule
        ]);

        $this->weekScheduleWithTimes[$this->weekTemplate[$dayName]] = $sendingTime->getSendingTime();

        $this->writeInfoLog('setSendingDayTimeAndDayQuizQuantity method executing before deb', [
            ' $this->weekScheduleWithQuizQuantities' =>  $this->weekScheduleWithQuizQuantities
        ]);

        if (!is_null($dayQuizQuantitySchedule)) {
            $this->weekScheduleWithQuizQuantities[$this->weekTemplate[$dayName]] =
                $dayQuizQuantitySchedule->getQuantity();
        }

        $this->writeInfoLog('setSendingDayTimeAndDayQuizQuantity method executing after deb', [
            ' $this->weekScheduleWithQuizQuantities' =>  $this->weekScheduleWithQuizQuantities
        ]);
    }

    public function unsetSendingDayTime(string $dayName, SendScheduleKind $sendScheduleKind): void
    {
        if (!in_array($dayName, array_keys($this->weekTemplate))) {
            throw new \Exception('Did not set sending day. Day doesn\'t match the template');
        }

        $this->weekSchedule ^= $this->weekTemplate[$dayName];

        if ($sendScheduleKind == SendScheduleKind::english_words) {
            $this->weekScheduleWithTimes[$this->weekTemplate[$dayName]] = '';
        }
        if ($sendScheduleKind == SendScheduleKind::quiz) {
            unset($this->weekScheduleWithTimes[$this->weekTemplate[$dayName]]);
            unset($this->weekScheduleWithQuizQuantities[$this->weekTemplate[$dayName]]);
        }

        $this->writeInfoLog('unsetSendingDayTime method executed', [
            'day name' => $dayName,
            'week schedule' => $this->weekSchedule,
            'day from week template' => $this->weekTemplate[$dayName],
            'week schedule with quiz quantities' => $this->weekScheduleWithQuizQuantities
        ]);
    }

    public function saveToUser(int $userId, SendScheduleKind $sendScheduleKind): static
    {
        $daysAndTimesInJson = json_encode($this->weekScheduleWithTimes, true);

        $updateConfig[$sendScheduleKind->name . '_week_and_times_sending_conf'] = $daysAndTimesInJson;
        $updateConfig[$sendScheduleKind->name . '_week_sending_conf'] = $this->weekSchedule;

        if (sizeof($this->weekScheduleWithQuizQuantities) > 0) {
            $daysAnsQuizQuantityInJson = json_encode($this->weekScheduleWithQuizQuantities, true);
            $updateConfig['quiz_quantity_sending_conf'] = $daysAnsQuizQuantityInJson;
        }

        User::where('id', $userId)->update($updateConfig);

        $this->writeInfoLog('Updated sending schedule for user', [
            'kind' => $sendScheduleKind->name,
            'user id' => $userId,
            'days and times' => $this->weekScheduleWithTimes,
            'days and quiz quantities' => $this->weekScheduleWithQuizQuantities
        ]);
        return $this;
    }

    public function saveToUserByChatId(string $chatId, SendScheduleKind $sendScheduleKind): static
    {
        $this->writeInfoLog('Updated sending schedule for user by chat id deb', [
            '$this->weekScheduleWithQuizQuantities' => $this->weekScheduleWithQuizQuantities
        ]);

        $daysAndTimesInJson = json_encode($this->weekScheduleWithTimes, true);

        $updateConfig[$sendScheduleKind->name . '_week_and_times_sending_conf'] = $daysAndTimesInJson;
        $updateConfig[$sendScheduleKind->name . '_week_sending_conf'] = $this->weekSchedule;

        if (sizeof($this->weekScheduleWithQuizQuantities) > 0) {
            $daysAnsQuizQuantityInJson = json_encode($this->weekScheduleWithQuizQuantities, true);
            $updateConfig['quiz_quantity_sending_conf'] = $daysAnsQuizQuantityInJson;
        }

        User::where('chat_id', $chatId)->update($updateConfig);

        $this->writeInfoLog('Updated sending schedule for user by chat id', [
            'kind' => $sendScheduleKind->name,
            'chat id' => $chatId,
            'days and times' => $this->weekScheduleWithTimes,
            'days and quiz quantities' => $this->weekScheduleWithQuizQuantities
        ]);
        return $this;
    }

    public function disableScheduleAndSaveToUser(int $userId, SendScheduleKind $sendScheduleKind)
    {
        User::where('id', $userId)->update([
            'is_enabled_' . $sendScheduleKind->name . '_sending' => 0,
            $sendScheduleKind->name . '_week_sending_conf' => 0,
            $sendScheduleKind->name . '_week_and_times_sending_conf' => null
        ]);

        $this->writeInfoLog('Disabled sending schedule', [
            'kind' => $sendScheduleKind->name,
            'user id' => $userId,
        ]);

        return null;
    }

    public function getFromUserForFrontEnd(int $userId, SendScheduleKind $sendScheduleKind): array
    {
        $data = [];
        $userModel = user::find($userId);

        $weekSchedule = $userModel->{$sendScheduleKind->name . '_week_sending_conf'};

        $daysAndTimesArray = json_decode(
            $userModel->{$sendScheduleKind->name . '_week_and_times_sending_conf'},
            true
        );

        $daysAnsQuizQuantityArray = json_decode($userModel->quiz_quantity_sending_conf, true);

        $this->writeInfoLog('Getting week schedule of user for front-end', [
            'kind' => $sendScheduleKind->name,
            'weekTemplate' => $this->weekTemplate,
            'schedule from DB' => [
                'days and times' => $daysAndTimesArray,
                'days and quiz quantities' => $daysAnsQuizQuantityArray,
                '$weekSchedule' => $weekSchedule
            ]
        ]);

        foreach ($this->weekTemplate as $weekTemplateItem) {
            if (($weekSchedule & $weekTemplateItem) > 0 && isset($daysAndTimesArray[$weekTemplateItem])) {
                $data['days'][] = true;
                $data['times'][] = $daysAndTimesArray[$weekTemplateItem];
                if ($sendScheduleKind == $sendScheduleKind::quiz) {
                    $data['quiz_quantities'][] = $daysAnsQuizQuantityArray[$weekTemplateItem];
                }
            } else {
                $data['days'][] = false;
                $data['times'][] = '';
                if ($sendScheduleKind == $sendScheduleKind::quiz) {
                    $data['quiz_quantities'][] = '';
                }
            }
        }

        $this->writeInfoLog('Got week schedule of user for front-end', [
            'kind' => $sendScheduleKind->name,
            'weekTemplate' => $this->weekTemplate,
            'schedule from DB' => [
                'days and times' => $daysAndTimesArray,
                'days and quiz quantities' => $daysAnsQuizQuantityArray
            ],
            'data' => $data
        ]);
        return $data;
    }

    public function getFromUserByChatId(
        string $chatId,
        SendScheduleKind $sendScheduleKind,
        ?int &$weekScheduleConfig = null,
        ?array &$daysAndTimesConfigArray = null,
        ?array &$daysAndQuizQuantityConfigArray = null
    ): array {
        $data = [];
        $userModel = User::where('chat_id', $chatId)->first();

        $weekScheduleConfig = $userModel->{$sendScheduleKind->name . '_week_sending_conf'};

        $daysAndTimesConfigArray = json_decode(
            $userModel->{$sendScheduleKind->name . '_week_and_times_sending_conf'},
            true
        );

        $daysAndQuizQuantityConfigArray = json_decode($userModel->quiz_quantity_sending_conf, true);

        $this->writeInfoLog('Getting week schedule of user for tlg menu by chatId', [
            'kind' => $sendScheduleKind->name,
            'weekTemplate' => $this->weekTemplate,
            'schedule from DB' => [
                'days and times config' => $daysAndTimesConfigArray,
                'days and quiz quantities' => $daysAndQuizQuantityConfigArray,
                '$weekScheduleConfig' => $weekScheduleConfig
            ]
        ]);

        foreach ($this->weekTemplate as $day => $weekTemplateItem) {
            if (($weekScheduleConfig & $weekTemplateItem) > 0 && isset($daysAndTimesConfigArray[$weekTemplateItem])) {
                $data['days'][] = true;
                $data['times'][] = $daysAndTimesConfigArray[$weekTemplateItem];
                if ($sendScheduleKind == $sendScheduleKind::quiz && isset($daysAndQuizQuantityConfigArray[$weekTemplateItem])) {
                    $data['quiz_quantities'][$day] = $daysAndQuizQuantityConfigArray[$weekTemplateItem];
                }
            } else {
                $data['days'][] = false;
                $data['times'][] = '';
                if ($sendScheduleKind == $sendScheduleKind::quiz && isset($daysAndQuizQuantityConfigArray[$weekTemplateItem])) {
                    $data['quiz_quantities'][$day] = '';
                }
            }
        }

        $this->writeInfoLog('Got week schedule of user for tlg menu by chatId', [
            'kind' => $sendScheduleKind->name,
            'weekTemplate' => $this->weekTemplate,
            'schedule from DB' => [
                'days and times config' => $daysAndTimesConfigArray,
                'days and quiz quantities' => $daysAndQuizQuantityConfigArray
            ],
            'week schedule' => $this->weekSchedule,
            'data' => $data
        ]);
        return $data;
    }

    public function getWeekSchedule(): int
    {
        return $this->weekSchedule;
    }

    public function setWeekSchedule(int $weekSchedule): void
    {
        $this->weekSchedule = $weekSchedule;
    }

    public function setWeekSchedulWithQuizQuantities(array $weekScheduleWithQuizQuantities): void
    {
        $this->weekScheduleWithQuizQuantities = $weekScheduleWithQuizQuantities;
    }

    public function setWeekScheduleWithTimes(array $weekScheduleWithTimes): void
    {
        $this->weekScheduleWithTimes = $weekScheduleWithTimes;
    }

    public function getWeekTemplate(): array
    {

        return $this->weekTemplate;
    }

    public function getWeekScheduleWithTimes()
    {
        return $this->weekScheduleWithTimes;
    }

    public function getWeekScheduleWithQuizQuantities()
    {
        return $this->weekScheduleWithQuizQuantities;
    }

    public function getNextScheduleSendDate(int $userId, SendScheduleKind $sendScheduleKind): array
    {
        $userModel = user::find($userId);

        $daysAndTimesArray = json_decode(
            $userModel->{$sendScheduleKind->name . '_week_and_times_sending_conf'},
            true
        );

        $nextSendingDay = null;
        $nextSendingTime = null;

        $today = new \DateTime('now');
        $today = $today->format('D');
        $currentTime = time();


        foreach ($daysAndTimesArray as $key => $item) {
            if ($key >= $this->weekTemplate[$today] && $currentTime < strtotime($item)) {
                $nextSendingDay = $key;
                $nextSendingTime = $item;
                break;
            }
        }

        if (is_null($nextSendingDay)) {
            $nextSendingDay = key($daysAndTimesArray);
            $nextSendingTime = current($daysAndTimesArray);
        }

        $nextSendingDayDecoded = null;

        foreach ($this->weekTemplate as $key => $weekTemplate) {
            if ($weekTemplate == $nextSendingDay) {
                $nextSendingDayDecoded = $key;
                break;
            }
        }

        $this->writeInfoLog('getNextScheduleSendDate method has executed', [
            'kind' => $sendScheduleKind->name,
            'weekTemplate' => $this->weekTemplate,
            'schedule from DB' => [
                'days and times' => $daysAndTimesArray,
            ],
            'today' => $today,
            'next sending day' => $nextSendingDay,
            'next sending day decoded' => $nextSendingDayDecoded,
            'next sending time' => $nextSendingTime
        ]);

        return [$nextSendingDayDecoded => $nextSendingTime];
    }

    public function getSendingDaysFromUserForTelegramMenuByChatId(
        string $chatId,
        SendScheduleKind $sendScheduleKind
    ): array {
        $data = [];
        $userModel = User::where('chat_id', $chatId)->first();

        $weekSchedule = $userModel->{$sendScheduleKind->name . '_week_sending_conf'};

        $daysAndTimesArray = json_decode(
            $userModel->{$sendScheduleKind->name . '_week_and_times_sending_conf'},
            true
        );

        $daysAndQuizQuantityArray = json_decode($userModel->quiz_quantity_sending_conf, true);

        $this->writeInfoLog('Getting week schedule of user for telegram menu by chat id', [
            'kind' => $sendScheduleKind->name,
            'weekTemplate' => $this->weekTemplate,
            'schedule from DB' => [
                'days and times' => $daysAndTimesArray,
                '$weekSchedule' => $weekSchedule
            ]
        ]);

        foreach ($this->weekTemplate as $weekTemplateKey => $weekTemplateItem) {
            if (($weekSchedule & $weekTemplateItem) > 0 && isset($daysAndTimesArray[$weekTemplateItem])) {
                $data['days'][$weekTemplateKey] = true;
            } else {
                $data['days'][$weekTemplateKey] = false;
            }
        }

        $this->writeInfoLog('Got week schedule of user for telegram menu by chat id', [
            'chat id' => $chatId,
            'kind' => $sendScheduleKind->name,
            'weekTemplate' => $this->weekTemplate,
            'schedule from DB' => [
                'days and times' => $daysAndTimesArray,
                'days and quiz quantities' => $daysAndQuizQuantityArray
            ],
            'data' => $data
        ]);
        return $data;
    }

    public function setSendingDay(string $dayName): void
    {
        if (!in_array($dayName, array_keys($this->weekTemplate))) {
            throw new \Exception('Did not set sending day. Day doesn\'t match the template');
        }

        $this->weekSchedule |= $this->weekTemplate[$dayName];

        $this->writeInfoLog('setSendingDay method executing', [
            'day name' => $dayName,
            'week schedule' => $this->weekSchedule,
            'day from week template' => $this->weekTemplate[$dayName],
        ]);
    }
}

<?php

namespace App\Services\DataStructures\EnglishWordsSchedule;

use App\Enums\SendScheduleKind;
use Mockery\Exception;

class DayTimesSchedule
{
    protected array $timesTemplate;
    protected string $sendingTime;

    public function setScheduleKind(SendScheduleKind $sendScheduleKind): static
    {
        $this->timesTemplate = config($sendScheduleKind->name . '.schedule_sending_times_template');
        return $this;
    }

    public function getSendingTime(): string
    {
        return $this->sendingTime;
    }

    public function setSendingTime(string $sendingTime): static
    {
        $this->sendingTime = $sendingTime;
        return clone $this;
    }

    public function getTimesTemplate(): array
    {
        return $this->timesTemplate;
    }
}

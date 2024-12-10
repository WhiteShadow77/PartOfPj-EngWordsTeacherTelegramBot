<?php

namespace App\Services\DataStructures\EnglishWordsSchedule;

class DayQuizQuantitySchedule
{
    protected int $quantity;

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return clone $this;
    }
}

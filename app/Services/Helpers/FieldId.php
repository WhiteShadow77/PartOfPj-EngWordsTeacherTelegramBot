<?php

namespace App\Services\Helpers;

use App\Models\User;

class FieldId
{
    public static function makeFromModel(User $userModel, string $time): string
    {
        return $userModel->id . '_' . $time;
    }

    public static function make(int $userId, string $time): string
    {
        return $userId . '_' . $time;
    }
}

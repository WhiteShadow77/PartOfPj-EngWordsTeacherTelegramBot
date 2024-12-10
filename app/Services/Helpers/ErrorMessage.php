<?php

namespace App\Services\Helpers;

class ErrorMessage
{
    public static function make(string $errorMessage)
    {
        $errorMesaageItems = explode(': ', $errorMessage);
        end($errorMesaageItems);
        return current($errorMesaageItems) . '.';
    }
}

<?php

namespace App\Services\Helpers;

class FileCacheId
{
    public static function make(int|string $fieldId): string
    {
        return $fieldId . '_files';
    }
}

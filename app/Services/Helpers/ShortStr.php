<?php

namespace App\Services\Helpers;

class ShortStr
{
    public static function IfLengthBiggerThen(string $source, int $biggerThenParam)
    {
        if (mb_strlen($source) > $biggerThenParam) {
            return mb_substr($source, 0, $biggerThenParam) . ' ...';
        }
        return $source;
    }
}

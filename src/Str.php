<?php

namespace VictorRenan\PixGenerator;

class Str
{
    public static function padlen(int $stringLen): string
    {
        return str_pad(strval($stringLen), 2, "0", STR_PAD_LEFT);
    }


    public static function random(int $n): string
    {
        return bin2hex(random_bytes($n / 2));
    }
}
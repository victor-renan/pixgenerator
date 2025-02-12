<?php

namespace VictorRenan\PixGenerator;

class Str
{
    public static function padlen(int $stringLen)
    {
        return str_pad(strval($stringLen), 2, "0", STR_PAD_LEFT);
    }


    public static function random(int $n)
    {
        return bin2hex(random_bytes($n));
    }
}
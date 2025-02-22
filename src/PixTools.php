<?php

namespace VictorRenan\PixGenerator;

class PixTools
{
    public static function makePart(string $id, ?string $text): string
    {
        if ($text == null) return '';
        return $id . PixTools::sizeStr($text) . $text;
    }

    public static function sizeInt(int $num): string
    {
        return str_pad(strval($num), 2, "0", STR_PAD_LEFT);
    }

    public static function sizeStr(string $str): string
    {
        return self::sizeInt(strlen($str));
    }

    public static function randomId(int $len = 6): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($chars), 0, $len);
    }
}
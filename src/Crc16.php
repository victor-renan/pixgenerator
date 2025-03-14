<?php

namespace VictorRenan\PixGenerator;

class Crc16
{
    private const POLINOM = 0x1021;

    public static function checksum(string $chars, int $nibbles = 4): string
    {
        $crc = 0xFFFF;

        for ($i = 0; $i < strlen($chars); $i++) {
            $crc = self::calculate($crc, ord($chars[$i]));
        }

        return substr(sprintf("%X", $crc), - $nibbles);
    }

    private static function calculate(int $crc, int $newByte): int
    {
        for ($i = 0; $i < 8; $i++) {
            if ((($crc & 0x8000) >> 8) ^ ($newByte & 0x80)) {
                $crc = ($crc << 1) ^ self::POLINOM;
            } else {
                $crc <<= 1;
            }
            $newByte <<= 1;
        }

        return $crc;
    }

}
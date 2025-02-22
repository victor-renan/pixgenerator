<?php

use PHPUnit\Framework\TestCase;
use VictorRenan\PixGenerator\Crc16;

final class Crc16Test extends TestCase {
    public function testCrc16ccitt(): void
    {
        $test = 'Test';
        $expected = '0x2888';
        $result = '0x' . Crc16::checksum($test);
        $this->assertEquals($result, $expected);
    }
}
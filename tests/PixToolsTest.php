<?php

use PHPUnit\Framework\TestCase;
use VictorRenan\PixGenerator\PixTools;
use VictorRenan\PixGenerator\PixGenerator;

final class PixToolsTest extends TestCase {
    public function testMakePixKeyPart(): void
    {
        $pixKey = 'test@mail.com';
        $result = PixGenerator::ID_MERCHANT_ACCOUNT_INFORMATION_CHAVE . '13' . $pixKey;
        $part = PixTools::makePart(PixGenerator::ID_MERCHANT_ACCOUNT_INFORMATION_CHAVE, $pixKey);
        $this->assertEquals($part, $result);
    }

    public function testPaddedSizeForInt(): void
    {
        $num = 7;
        $result = '07';
        $pad = PixTools::sizeInt($num);
        $this->assertEquals($pad, $result);
    }

    public function testPaddedSizeForStr(): void
    {
        $text = 'Test';
        $result = '04';
        $pad = PixTools::sizeStr($text);
        $this->assertEquals($pad, $result);
    }

    public function testRandomIdHasCorrectLength(): void
    {
        $text = PixTools::randomId();
        $this->assertEquals(strlen($text), 6);
    }
}
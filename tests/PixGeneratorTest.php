<?php

use PHPUnit\Framework\TestCase;
use VictorRenan\PixGenerator\PixGenerator;
use VictorRenan\PixGenerator\PixException;

final class PixGeneratorTest extends TestCase 
{
    private string $pixKey = 'teste@mail.com';

    public function testAdditionalInfoLengthLimits(): void
    {
        $limit = PixGenerator::MAX_MERCHANT_INFO_LEN 
                 - (4 + strlen(PixGenerator::GUI)) 
                 - (4 + strlen($this->pixKey)) 
                 - 4;

        $acceptable = new PixGenerator($this->pixKey);
        $acceptable->setAdditionalInfo(str_repeat('A', $limit));
        $this->assertNotEmpty($acceptable->getCode());

        $this->expectException(PixException::class);
        $exceed = new PixGenerator($this->pixKey);
        $exceed->setAdditionalInfo(str_repeat('A', $limit + 1));
        $exceed->getCode();
    }

    public function testAmountLengthLimit(): void
    {
        $acceptable = new PixGenerator($this->pixKey);
        $acceptable->setTransactionAmount(9999999999.99); 
        $this->assertNotEmpty($acceptable->getCode());

        $this->expectException(PixException::class);
        $exceed = new PixGenerator($this->pixKey);
        $exceed->setTransactionAmount(99999999999.00); 
        $exceed->getCode();
    }

    public function testMerchantNameLengthLimit(): void
    {
        $acceptable = new PixGenerator($this->pixKey);
        $acceptable->setMerchantName(str_repeat('A', PixGenerator::MAX_MERCHANT_NAME_LEN));
        $this->assertNotEmpty($acceptable->getCode());

        $this->expectException(PixException::class);
        $exceed = new PixGenerator($this->pixKey);
        $exceed->setMerchantName(str_repeat('A', PixGenerator::MAX_MERCHANT_NAME_LEN + 1));
        $exceed->getCode();
    }

    public function testMerchantCityLengthLimit(): void
    {
        $acceptable = new PixGenerator($this->pixKey);
        $acceptable->setMerchantCity(str_repeat('A', PixGenerator::MAX_MERCHANT_CITY_LEN));
        $this->assertNotEmpty($acceptable->getCode());

        $this->expectException(PixException::class);
        $exceed = new PixGenerator($this->pixKey);
        $exceed->setMerchantCity(str_repeat('A', PixGenerator::MAX_MERCHANT_CITY_LEN + 1));
        $exceed->getCode();
    }

    public function testTransactionIdLengthLimit(): void
    {
        $maxStrLen = PixGenerator::MAX_ADITIONAL_DATA_FIELD_TEMPLATE_LEN - 4;

        $acceptable = new PixGenerator($this->pixKey);
        $acceptable->setTransactionId(str_repeat('A', $maxStrLen));
        $this->assertNotEmpty($acceptable->getCode());

        $this->expectException(PixException::class);
        $exceed = new PixGenerator($this->pixKey);
        $exceed->setTransactionId(str_repeat('A', $maxStrLen + 1)); // Corrigido de setMerchantCity
        $exceed->getCode();
    }
}

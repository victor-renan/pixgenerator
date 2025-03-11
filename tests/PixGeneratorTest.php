<?php

use PHPUnit\Framework\TestCase;
use VictorRenan\PixGenerator\PixGenerator;
use VictorRenan\PixGenerator\PixException;

final class PixGeneratorTest extends TestCase {
    public function testPixKeyLengthLimits(): void
    {
        $limit = PixGenerator::MAX_MERCHANT_INFO_LEN - strlen(PixGenerator::GUI) - 8;

        $acceptable = new PixGenerator(random_bytes($limit));
        $acceptable->getCode();

        $this->expectException(PixException::class);

        $exceed = new PixGenerator(random_bytes($limit+1));
        $exceed->getCode();   
    }

    public function testAdditionalInfoLengthLimits(): void
    {
        $pixKey = 'teste@mail.com';
        $limit = PixGenerator::MAX_MERCHANT_INFO_LEN - strlen(PixGenerator::GUI) - strlen($pixKey) - 3 * 4;

        $acceptable = new PixGenerator($pixKey);
        $acceptable->setAdditionalInfo(random_bytes($limit));
        $acceptable->getCode();

        $this->expectException(PixException::class);

        $exceed = new PixGenerator($pixKey);
        $exceed->setAdditionalInfo(random_bytes($limit+1));
        $exceed->getCode();
    }

    public function testAmountLengthLimit(): void
    {
        $pixKey = 'teste@gmail.com';
        $valid = 9999999999.00;
        $invalid = 99999999999.00;

        $acceptable = new PixGenerator($pixKey);
        $acceptable->setTransactionAmount($valid);
        
        $this->expectException(PixException::class);

        $exceed = new PixGenerator($pixKey);
        $exceed->setTransactionAmount($invalid);
        $exceed->getCode();
    }

    public function testMerchantNameLengthLimit(): void
    {
        $pixKey = 'teste@gmail.com';

        $acceptable = new PixGenerator($pixKey);
        $acceptable->setMerchantName(random_bytes(PixGenerator::MAX_MERCHANT_NAME_LEN));
        $acceptable->getCode();

        $this->expectException(PixException::class);

        $exceed = new PixGenerator($pixKey);
        $exceed->setMerchantName(random_bytes(PixGenerator::MAX_MERCHANT_NAME_LEN+1));
        $exceed->getCode();
    }

    public function testMerchantCityLengthLimit(): void
    {
        $pixKey = 'teste@gmail.com';

        $acceptable = new PixGenerator($pixKey);
        $acceptable->setMerchantCity(random_bytes(PixGenerator::MAX_MERCHANT_CITY_LEN));
        $acceptable->getCode();

        $this->expectException(PixException::class);

        $exceed = new PixGenerator($pixKey);
        $exceed->setMerchantCity(random_bytes(PixGenerator::MAX_MERCHANT_CITY_LEN+1));
        $exceed->getCode();
    }

    public function testTransactionIdLengthLimit(): void
    {
        $pixKey = 'teste@gmail.com';

        $acceptable = new PixGenerator($pixKey);
        $acceptable->setTransactionId(random_bytes(PixGenerator::MAX_ADITIONAL_DATA_FIELD_TEMPLATE_LEN - 4));
        $acceptable->getCode();

        $this->expectException(PixException::class);

        $exceed = new PixGenerator($pixKey);
        $exceed->setMerchantCity(random_bytes(PixGenerator::MAX_ADITIONAL_DATA_FIELD_TEMPLATE_LEN+1 - 4));
        $exceed->getCode();
    }
}
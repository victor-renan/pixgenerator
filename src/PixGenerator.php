<?php

namespace VictorRenan\PixGenerator;

class PixGenerator
{
    public const MAX_MERCHANT_INFO_LEN = 99;
    public const MAX_AMOUNT_LEN = 13;
    public const MAX_MERCHANT_NAME_LEN = 25;
    public const MAX_MERCHANT_CITY_LEN = 15;
    public const MAX_ADITIONAL_DATA_FIELD_TEMPLATE_LEN = 29;

    public const QRCPSMCM = '000201';

    public const GUI = 'br.gov.bcb.pix';
    public const TRANSACTION_CURRENCY = '986';
    public const COUNTRY_CODE = 'BR';
    public const MERCHANT_CATEGORY = '0000';

    public const DEFAULT_MERCHANT_CITY = 'CIDADE';
    public const DEFAULT_MERCHANT_NAME = 'RECEBEDOR';

    public const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
    public const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
    public const ID_MERCHANT_ACCOUNT_INFORMATION_CHAVE = '01';
    public const ID_MERCHANT_ACCOUNT_INFORMATION_ADDITIONAL_INFO = '02';
    public const ID_MERCHANT_CATEGORY_CODE = '52';
    public const ID_TRANSACTION_CODE = '53';
    public const ID_MERCHANT_NAME = '59';
    public const ID_MERCHANT_CITY = '60';
    public const ID_COUNTRY_CODE = '58';
    public const ID_TRANSACTION_AMOUNT = '54';
    public const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
    public const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID_ID = '05';
    public const ID_CRC16 = '63';

    public string $chavePix;
    public ?float $transactionAmount = null;
    public string $transactionId;
    public string $merchantName;
    public string $merchantCity;
    public ?string $additionalInfo = null;

    public function __construct(string $chavePix)
    {
        if ($chavePix === '') {
            throw new PixException('Chave precisa ter no mínimo 1 caractere!');
        }

        $this->chavePix = $chavePix;
        $this->transactionId = PixTools::randomId();
        $this->merchantName = self::DEFAULT_MERCHANT_NAME;
        $this->merchantCity = self::DEFAULT_MERCHANT_CITY;
    }

    public function setTransactionAmount(float $transactionAmount): PixGenerator
    {
        $this->transactionAmount = $transactionAmount;
        return $this;
    }

    public function setTransactionId(string $transactionId): PixGenerator
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function setMerchantName(string $merchantName): PixGenerator
    {
        $this->merchantName = $merchantName;
        return $this;
    }

    public function setMerchantCity(string $merchantCity): PixGenerator
    {
        $this->merchantCity = $merchantCity;
        return $this;
    }

    public function setAdditionalInfo(string $additionalInfo): PixGenerator
    {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }

    public function getCode(): string
    {
        $content = self::QRCPSMCM;

        $this->prepareMerchantAccountInformation($content);
        $this->prepareMerchantCategoryCode($content);
        $this->prepareTransactionCurrency($content);
        $this->prepareTransactionAmount($content);
        $this->prepareCountryCode($content);
        $this->prepareMerchantName($content);
        $this->prepareMerchantCity($content);
        $this->prepareAdditionalDataFieldTemplate($content);
        $this->prepareCRC16($content);

        return $content;
    }

    private function prepareMerchantAccountInformation(string &$content)
    {
        $gui =  PixTools::makePart(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, self::GUI);
        $chave =  PixTools::makePart(self::ID_MERCHANT_ACCOUNT_INFORMATION_CHAVE, $this->chavePix);
        $additionalInfo =  PixTools::makePart(self::ID_MERCHANT_ACCOUNT_INFORMATION_ADDITIONAL_INFO, $this->additionalInfo);

        $bodySize = strlen($gui) + strlen($chave) + strlen($additionalInfo);

        if ($bodySize <= self::MAX_MERCHANT_INFO_LEN) {
            $merchant =  $gui . $chave . $additionalInfo;
            $content .= PixTools::makePart(self::ID_MERCHANT_ACCOUNT_INFORMATION, $merchant);
            return;
        }

        if ($this->additionalInfo != null) {
            throw new PixException('Informação adicional excede o limite de tamanho!');
        }

        throw new PixException('Chave excede o limite de tamanho!');
    }

    private function prepareMerchantCategoryCode(string &$content)
    {
        $content .= PixTools::makePart(self::ID_MERCHANT_CATEGORY_CODE, self::MERCHANT_CATEGORY);
    }

    private function prepareTransactionCurrency(string &$content)
    {
        $content .= PixTools::makePart(self::ID_TRANSACTION_CODE, self::TRANSACTION_CURRENCY);
    }

    private function prepareTransactionAmount(string &$content)
    {
        if ($this->transactionAmount == null) {
            return;
        }

        $amount = number_format($this->transactionAmount, 2, '.', '');

        if (strlen($amount) > self::MAX_AMOUNT_LEN) {
            throw new PixException('Valor da transação excede o limite!');
        }

        $content .= PixTools::makePart(self::ID_TRANSACTION_AMOUNT, strval($amount));
    }

    private function prepareCountryCode(string &$content)
    {
        $content .= PixTools::makePart(self::ID_COUNTRY_CODE, self::COUNTRY_CODE);
    }

    private function prepareMerchantName(string &$content)
    {
        if (strlen($this->merchantName) > self::MAX_MERCHANT_NAME_LEN) {
            throw new PixException('Nome do recebedor excede o limite de tamanho!');
        }

        $content .= PixTools::makePart(self::ID_MERCHANT_NAME, $this->merchantName);
    }

    private function prepareMerchantCity(string &$content)
    {
        if (strlen($this->merchantCity) > self::MAX_MERCHANT_CITY_LEN) {
            throw new PixException('Nome da cidade excede o limite de tamanho!');
        }

        $content .= PixTools::makePart(self::ID_MERCHANT_CITY, $this->merchantCity);
    }

    private function prepareAdditionalDataFieldTemplate(string &$content)
    {
        $transactionId =  PixTools::makePart(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID_ID, $this->transactionId);

        if (strlen($transactionId) > self::MAX_ADITIONAL_DATA_FIELD_TEMPLATE_LEN) {
            throw new PixException('Id da transação excede o limite de tamanho!');
        }

        $content .= PixTools::makePart(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $transactionId);
    }

    private function prepareCRC16(string &$content)
    {
        $content .= self::ID_CRC16 . '04';
        $content .= Crc16::checksum($content);
    }
}
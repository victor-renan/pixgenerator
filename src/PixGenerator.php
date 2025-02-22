<?php

namespace VictorRenan\PixGenerator;

class PixGenerator
{
    private const MAX_MERCHANT_INFO_LEN = 99;
    private const MAX_AMOUNT_LEN = 13;
    private const MAX_MERCHANT_NAME_LEN = 25;
    private const MAX_MERCHANT_CITY_LEN = 15;
    private const MAX_ADITIONAL_DATA_FIELD_TEMPLATE_LEN = 29;

    private const QRCPSMCM = '000201';

    private const DEFAULT_GUI = 'br.gov.bcb.pix';
    private const DEFAULT_MERCHANT_CITY = 'CIDADE';
    private const DEFAULT_MERCHANT_NAME = 'RECEBEDOR';
    private const DEFAULT_TRANSACTION_CURRENCY = '986';
    private const DEFAULT_COUNTRY_CODE = 'BR';
    private const DEFAULT_MERCHANT_CATEGORY = '0000';

    private const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
    private const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
    private const ID_MERCHANT_ACCOUNT_INFORMATION_CHAVE = '01';
    private const ID_MERCHANT_ACCOUNT_INFORMATION_ADDITIONAL_INFO = '02';
    private const ID_MERCHANT_CATEGORY_CODE = '52';
    private const ID_TRANSACTION_CODE = '53';
    private const ID_MERCHANT_NAME = '59';
    private const ID_MERCHANT_CITY = '60';
    private const ID_COUNTRY_CODE = '58';
    private const ID_TRANSACTION_AMOUNT = '54';
    private const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
    private const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID_ID = '05';
    private const ID_CRC16 = '63';

    public string $chavePix;
    public ?float $transactionAmount = null;
    public string $transactionId;
    public string $merchantName;
    public string $merchantCity;
    public ?string $additionalInfo = null;

    public function __construct(string $chavePix)
    {
        $this->chavePix = $chavePix;
        $this->transactionId = Str::random(6);
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
        $gui = self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI
            . Str::padlen(strlen(self::DEFAULT_GUI))
            . self::DEFAULT_GUI;

        $chave = self::ID_MERCHANT_ACCOUNT_INFORMATION_CHAVE
            . Str::padlen(strlen($this->chavePix))
            . $this->chavePix;

        $additionalInfo = '';

        $bodySize = strlen($gui) + strlen($chave);

        if ($this->additionalInfo != null) {
            $additionalInfo = self::ID_MERCHANT_ACCOUNT_INFORMATION_ADDITIONAL_INFO
                . Str::padlen(strlen($this->additionalInfo))
                . $this->additionalInfo;
            $bodySize += strlen($additionalInfo);
        }

        if ($bodySize > self::MAX_MERCHANT_INFO_LEN) {
            throw new PixException('Merchant Info exceeds the maximum length!');
        }

        $content .= self::ID_MERCHANT_ACCOUNT_INFORMATION
            . Str::padlen($bodySize)
            . $gui
            . $chave
            . $additionalInfo;
    }

    private function prepareMerchantCategoryCode(string &$content)
    {
        $content .= self::ID_MERCHANT_CATEGORY_CODE
            . Str::padlen(strlen(self::DEFAULT_MERCHANT_CATEGORY))
            . self::DEFAULT_MERCHANT_CATEGORY;
    }

    private function prepareTransactionCurrency(string &$content)
    {
        $content .= self::ID_TRANSACTION_CODE
            . Str::padlen(strlen(self::DEFAULT_TRANSACTION_CURRENCY))
            . self::DEFAULT_TRANSACTION_CURRENCY;
    }

    private function prepareTransactionAmount(string &$content)
    {
        if (empty($this->transactionAmount)) {
            return;
        }

        $amount = number_format($this->transactionAmount, 2, '.');

        if (strlen($amount) > self::MAX_AMOUNT_LEN) {
            throw new PixException('Transaction Ammount is exceeds the maximum limit!');
        }

        $content .= self::ID_TRANSACTION_AMOUNT
            . Str::padlen(strlen($amount))
            . strval($amount);
    }

    private function prepareCountryCode(string &$content)
    {
        $content .= self::ID_COUNTRY_CODE
            . Str::padlen(strlen(self::DEFAULT_COUNTRY_CODE))
            . self::DEFAULT_COUNTRY_CODE;
    }

    private function prepareMerchantName(string &$content)
    {
        if (strlen($this->merchantName) > self::MAX_MERCHANT_NAME_LEN) {
            throw new PixException('Merchant Name exceeds the maximum length!');
        }

        $content .= self::ID_MERCHANT_NAME
            . Str::padlen(strlen($this->merchantName))
            . $this->merchantName;
    }

    private function prepareMerchantCity(string &$content)
    {
        if (strlen($this->merchantCity) > self::MAX_MERCHANT_CITY_LEN) {
            throw new PixException('Merchant City exceeds the maximum length!');
        }

        $content .= self::ID_MERCHANT_CITY
            . Str::padlen(strlen($this->merchantCity))
            . $this->merchantCity;
    }

    private function prepareAdditionalDataFieldTemplate(string &$content)
    {
        $transactionId = self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID_ID
            . Str::padlen(strlen($this->transactionId))
            . $this->transactionId;

        if (strlen($transactionId) > self::MAX_ADITIONAL_DATA_FIELD_TEMPLATE_LEN) {
            throw new PixException('Additional Data Field Template exceeds the maximum length!');
        }

        $content .= self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE
            . Str::padlen(strlen($transactionId))
            . $transactionId;
    }

    private function prepareCRC16(string &$content)
    {
        $content .= self::ID_CRC16 . '04';
        $content .= Crc16::checksum($content);
    }
}
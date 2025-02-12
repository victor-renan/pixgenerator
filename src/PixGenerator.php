<?php

namespace VictorRenan\PixGenerator;

class PixGenerator
{
    private const MAX_CODE_LEN = 99;
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
    public ?float $transactionAmount;
    public string $transactionId;
    public string $merchantName;
    public string $merchantCity;
    public ?string $additionalInfo;

    public function __construct(
        string $chavePix,
        ?float $transactionAmount = null,
        ?string $transactionId = null,
        ?string $merchantName = null,
        ?string $merchantCity = null,
        ?string $additionalInfo = null,
    ) {
        $this->chavePix = $chavePix;
        $this->transactionAmount = $transactionAmount;
        $this->transactionId = $transactionId ?: Str::random(6);
        $this->merchantName = $merchantName ?: self::DEFAULT_MERCHANT_NAME;
        $this->merchantCity = $merchantCity ?: self::DEFAULT_MERCHANT_CITY;
        $this->additionalInfo = $additionalInfo;
    }

    public function code(): string
    {
        $content = self::QRCPSMCM;

        $this->setMerchantAccountInformation($content);
        $this->setMerchantCategoryCode($content);
        $this->setTransactionCurrency($content);
        $this->setTransactionAmount($content);
        $this->setCountryCode($content);
        $this->setMerchantName($content);
        $this->setMerchantCity($content);
        $this->setAdditionalDataFieldTemplate($content);
        $this->setCRC16($content);

        return $content;
    }

    private function setMerchantAccountInformation(string &$content)
    {
        $gui =
            self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI
            . Str::padlen(strlen(self::DEFAULT_GUI))
            . self::DEFAULT_GUI;

        $chave =
            self::ID_MERCHANT_ACCOUNT_INFORMATION_CHAVE
            . Str::padlen(strlen($this->chavePix))
            . $this->chavePix;

        $content .= self::ID_MERCHANT_ACCOUNT_INFORMATION;

        if ($this->additionalInfo != null) {
            $additionalInfo =
                self::ID_MERCHANT_ACCOUNT_INFORMATION_ADDITIONAL_INFO
                . Str::padlen(strlen($this->additionalInfo))
                . $this->additionalInfo;

            $content .=
                Str::padlen(strlen(self::DEFAULT_GUI . $this->chavePix . $this->additionalInfo) + 3 * 4)
                . $gui
                . $chave
                . $additionalInfo;
        } else {
            $content .=
                Str::padlen(strlen(self::DEFAULT_GUI . $this->chavePix) + 2 * 4)
                . $gui
                . $chave;
        }
    }

    private function setMerchantCategoryCode(string &$content)
    {

        $content .=
            self::ID_MERCHANT_CATEGORY_CODE
            . Str::padlen(strlen(self::DEFAULT_MERCHANT_CATEGORY))
            . self::DEFAULT_MERCHANT_CATEGORY;
    }

    private function setTransactionCurrency(string &$content)
    {
        $content .=
            self::ID_TRANSACTION_CODE
            . Str::padlen(strlen(self::DEFAULT_TRANSACTION_CURRENCY))
            . self::DEFAULT_TRANSACTION_CURRENCY;
    }

    private function setTransactionAmount(string &$content)
    {
        if (isset($this->transactionAmount)) {
            $amount = number_format($this->transactionAmount, 2, '.');
            $content .=
                self::ID_TRANSACTION_AMOUNT
                . Str::padlen(strlen(strval($amount)))
                . strval($amount);
        }
    }

    private function setCountryCode(string &$content)
    {
        $content .=
            self::ID_COUNTRY_CODE
            . Str::padlen(strlen(self::DEFAULT_COUNTRY_CODE))
            . self::DEFAULT_COUNTRY_CODE;
    }

    private function setMerchantName(string &$content)
    {
        $content .=
            self::ID_MERCHANT_NAME
            . Str::padlen(strlen($this->merchantName))
            . $this->merchantName;
    }

    private function setMerchantCity(string &$content)
    {
        $content .=
            self::ID_MERCHANT_CITY
            . Str::padlen(strlen($this->merchantCity))
            . $this->merchantCity;
    }

    private function setAdditionalDataFieldTemplate(string &$content)
    {
        $content .=
            self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE
            . Str::padlen(strlen($this->transactionId) + 4)
            . self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID_ID
            . Str::padlen(strlen($this->transactionId))
            . $this->transactionId;
    }

    private function setCRC16(string &$content)
    {
        $content .= self::ID_CRC16 . '04';
        $content .= CRC16::make($content);
    }
}
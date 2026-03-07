<?php

namespace VictorRenan\PixGenerator;

/**
 * Gerador de códigos PIX estáticos (copia e cola)
 * 
 * Esta classe gera códigos PIX no formato EMV para QR Codes estáticos,
 * seguindo o Manual de Padrões para Iniciação do PIX do Banco Central do Brasil.
 */
class PixGenerator
{
    public const MAX_MERCHANT_INFO_LEN = 99;
    public const MAX_AMOUNT_LEN = 13;
    public const MAX_MERCHANT_NAME_LEN = 25;
    public const MAX_MERCHANT_CITY_LEN = 15;
    public const MAX_ADDITIONAL_DATA_FIELD_TEMPLATE_LEN = 29;

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

    public PixValidator $validator;

    /**
     * Cria uma nova instância do gerador PIX
     * 
     * @param string $chavePix Chave PIX (CPF, CNPJ, email, telefone ou chave aleatória)
     * @param bool $validateOnConstruct Se deve validar a chave no construtor (padrão: true)
     * @throws PixException Se a chave PIX for inválida
     */
    public function __construct(string $chavePix, bool $validateOnConstruct = true)
    {
        $this->validator = new PixValidator($chavePix);
        if ($validateOnConstruct) {
            $this->validator->validate();
        }

        $this->chavePix = $chavePix;
        $this->transactionId = PixTools::randomId();
        $this->merchantName = self::DEFAULT_MERCHANT_NAME;
        $this->merchantCity = self::DEFAULT_MERCHANT_CITY;
    }

    /**
     * Define o valor da transação PIX
     * 
     * @param float $transactionAmount Valor em reais (deve ser positivo)
     * @return self Para encadeamento de métodos (fluent interface)
     * @throws PixException Se o valor for negativo ou zero
     */
    public function setTransactionAmount(float $transactionAmount): PixGenerator
    {
        if ($transactionAmount <= 0) {
            throw new PixException('O valor da transação deve ser positivo');
        }
        $this->transactionAmount = $transactionAmount;
        return $this;
    }

    /**
     * Define o ID da transação
     * 
     * @param string $transactionId ID único da transação
     * @return self Para encadeamento de métodos
     */
    public function setTransactionId(string $transactionId): PixGenerator
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * Define o nome do recebedor
     * 
     * @param string $merchantName Nome do recebedor (máximo 25 caracteres)
     * @return self Para encadeamento de métodos
     * @throws PixException Se o nome exceder o limite de tamanho
     */
    public function setMerchantName(string $merchantName): PixGenerator
    {
        $sanitized = PixTools::sanitizeText($merchantName);
        if (strlen($sanitized) > self::MAX_MERCHANT_NAME_LEN) {
            throw new PixException('Nome do recebedor excede o limite de tamanho!');
        }
        $this->merchantName = $sanitized;
        return $this;
    }

    /**
     * Define a cidade do recebedor
     * 
     * @param string $merchantCity Cidade do recebedor (máximo 15 caracteres)
     * @return self Para encadeamento de métodos
     * @throws PixException Se o nome da cidade exceder o limite de tamanho
     */
    public function setMerchantCity(string $merchantCity): PixGenerator
    {
        $sanitized = PixTools::sanitizeText($merchantCity);
        if (strlen($sanitized) > self::MAX_MERCHANT_CITY_LEN) {
            throw new PixException('Nome da cidade excede o limite de tamanho!');
        }
        $this->merchantCity = $sanitized;
        return $this;
    }

    /**
     * Define informações adicionais sobre a transação
     * 
     * @param string $additionalInfo Informações adicionais
     * @return self Para encadeamento de métodos
     */
    public function setAdditionalInfo(string $additionalInfo): PixGenerator
    {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }

    /**
     * Gera o código PIX completo
     * 
     * @return string Código PIX no formato EMV para QR Code
     * @throws PixException Se algum campo exceder os limites estabelecidos
     */
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

        if (strlen($transactionId) > self::MAX_ADDITIONAL_DATA_FIELD_TEMPLATE_LEN) {
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

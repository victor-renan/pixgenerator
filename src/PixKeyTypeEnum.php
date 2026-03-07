<?php

namespace VictorRenan\PixGenerator;

/**
 * Enumeração dos tipos de chave PIX aceitos
 */
enum PixKeyTypeEnum: string {
    case CPF = 'cpf';
    case CNPJ = 'cnpj';
    case PHONE = 'phone';
    case UUID = 'uuid';
    case EMAIL = 'email';

    /**
     * Valida se uma string corresponde ao tipo de chave PIX
     * 
     * @param string $k Chave PIX a ser validada
     * @return bool True se a chave for válida para este tipo
     */
    public function validate(string $k): bool {
        return match($this) {
            self::CPF   => self::validateCPF($k),
            self::CNPJ  => self::validateCNPJ($k),
            self::PHONE => (bool) preg_match('/^\+55\d{11}$/', $k),
            self::UUID  => (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $k),
            self::EMAIL => filter_var($k, FILTER_VALIDATE_EMAIL) && strlen($k) <= 77,
        };
    }

    /**
     * Valida um CPF verificando formato e dígitos verificadores
     * 
     * @param string $cpf CPF a ser validado (apenas números)
     * @return bool True se o CPF for válido
     */
    private static function validateCPF(string $cpf): bool {
        if (!preg_match('/^\d{11}$/', $cpf)) {
            return false;
        }

        // Rejeita CPFs com todos os dígitos iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        if (intval($cpf[9]) !== $digit1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        return intval($cpf[10]) === $digit2;
    }

    /**
     * Valida um CNPJ verificando formato e dígitos verificadores
     * 
     * @param string $cnpj CNPJ a ser validado (apenas números)
     * @return bool True se o CNPJ for válido
     */
    private static function validateCNPJ(string $cnpj): bool {
        if (!preg_match('/^\d{14}$/', $cnpj)) {
            return false;
        }

        // Rejeita CNPJs com todos os dígitos iguais
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += intval($cnpj[$i]) * $weights1[$i];
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        if (intval($cnpj[12]) !== $digit1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += intval($cnpj[$i]) * $weights2[$i];
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        return intval($cnpj[13]) === $digit2;
    }
}

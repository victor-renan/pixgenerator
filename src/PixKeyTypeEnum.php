<?php

namespace VictorRenan\PixGenerator;

enum PixKeyTypeEnum: string {
    case CPF = 'cpf';
    case CNPJ = 'cnpj';
    case PHONE = 'phone';
    case UUID = 'uuid';
    case EMAIL = 'email';

    public function validate(string $k): bool {
        return match($this) {
            self::CPF   => (bool) preg_match('/^\d{11}$/', $k),
            self::CNPJ  => (bool) preg_match('/^\d{14}$/', $k),
            self::PHONE => (bool) preg_match('/^\+55\d{11}$/', $k),
            self::UUID  => (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $k),
            self::EMAIL => filter_var($k, FILTER_VALIDATE_EMAIL) && strlen($k) <= 77,
        };
    }
}

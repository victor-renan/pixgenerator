<?php

namespace VictorRenan\PixGenerator;

/**
 * Implementação do algoritmo CRC-16 (Cyclic Redundancy Check)
 * 
 * Usado para gerar o checksum do código PIX conforme especificação EMV.
 * Utiliza o polinômio CCITT (0x1021).
 */
class Crc16
{
    /**
     * Polinômio CCITT usado no cálculo do CRC-16
     * 
     * @var int 0x1021 é o polinômio padrão para CRC-16-CCITT
     */
    private const POLINOM = 0x1021;

    /**
     * Valor inicial do CRC (todos os bits em 1)
     * 
     * @var int 0xFFFF é o valor inicial padrão do CRC-16-CCITT
     */
    private const CRC_INIT = 0xFFFF;

    /**
     * Máscara para o bit mais significativo (MSB)
     * 
     * @var int 0x8000 representa o 16º bit
     */
    private const MSB_MASK = 0x8000;

    /**
     * Máscara para o bit mais significativo de um byte
     * 
     * @var int 0x80 representa o 8º bit
     */
    private const BYTE_MSB_MASK = 0x80;

    /**
     * Calcula o checksum CRC-16 de uma string
     * 
     * @param string $chars String para calcular o checksum
     * @param int $nibbles Número de dígitos hexadecimais a retornar (padrão: 4)
     * @return string Checksum em formato hexadecimal
     */
    public static function checksum(string $chars, int $nibbles = 4): string
    {
        $crc = self::CRC_INIT;

        for ($i = 0; $i < strlen($chars); $i++) {
            $crc = self::calculate($crc, ord($chars[$i]));
        }

        return substr(sprintf("%X", $crc), - $nibbles);
    }

    /**
     * Calcula o CRC-16 processando um byte adicional
     * 
     * @param int $crc Valor atual do CRC
     * @param int $newByte Novo byte a ser processado
     * @return int Novo valor do CRC
     */
    private static function calculate(int $crc, int $newByte): int
    {
        // Processa cada bit do byte
        for ($i = 0; $i < 8; $i++) {
            // Verifica se o MSB do CRC XOR com o MSB do byte é 1
            if ((($crc & self::MSB_MASK) >> 8) ^ ($newByte & self::BYTE_MSB_MASK)) {
                // Shift left e aplica o polinômio via XOR
                $crc = ($crc << 1) ^ self::POLINOM;
            } else {
                // Apenas shift left
                $crc <<= 1;
            }
            // Move para o próximo bit do byte
            $newByte <<= 1;
        }

        return $crc;
    }

}

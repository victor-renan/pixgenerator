<?php

namespace VictorRenan\PixGenerator;

/**
 * Classe utilitária com métodos auxiliares para geração de códigos PIX
 */
class PixTools
{
    /**
     * Cria uma parte do código PIX no formato ID + Tamanho + Conteúdo
     * 
     * @param string $id Identificador do campo
     * @param string|null $text Conteúdo do campo
     * @return string Parte formatada ou string vazia se o texto for null
     */
    public static function makePart(string $id, ?string $text): string
    {
        if ($text == null) return '';
        return $id . PixTools::sizeStr($text) . $text;
    }

    /**
     * Formata um número inteiro com padding para 2 dígitos
     * 
     * @param int $num Número a ser formatado
     * @return string Número formatado com 2 dígitos
     */
    public static function sizeInt(int $num): string
    {
        return str_pad(strval($num), 2, "0", STR_PAD_LEFT);
    }

    /**
     * Retorna o tamanho de uma string formatado com 2 dígitos
     * 
     * @param string $str String para calcular o tamanho
     * @return string Tamanho formatado com 2 dígitos
     */
    public static function sizeStr(string $str): string
    {
        return self::sizeInt(strlen($str));
    }

    /**
     * Gera um ID aleatório criptograficamente seguro
     * 
     * @param int $len Tamanho do ID a ser gerado (padrão: 6)
     * @return string ID aleatório gerado
     * @throws \Exception Se não for possível gerar bytes aleatórios
     */
    public static function randomId(int $len = 6): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($chars) - 1;
        $id = '';
        for ($i = 0; $i < $len; $i++) {
            $id .= $chars[random_int(0, $max)];
        }
        return $id;
    }

    /**
     * Remove caracteres especiais de um texto, mantendo apenas letras, números e espaços
     * 
     * @param string $text Texto a ser sanitizado
     * @return string Texto sanitizado
     */
    public static function sanitizeText(string $text): string
    {
        // Remove acentos e normaliza caracteres
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        // Remove caracteres especiais, mantém apenas alfanuméricos e espaços
        $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
        // Remove espaços múltiplos
        $text = preg_replace('/\s+/', ' ', $text);
        return trim(strtoupper($text));
    }
}
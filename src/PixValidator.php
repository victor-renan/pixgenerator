<?php

namespace VictorRenan\PixGenerator;

/**
 * Validador de chaves PIX
 * 
 * Verifica se uma chave PIX é válida segundo os padrões do Banco Central do Brasil.
 * Suporta CPF, CNPJ, telefone, email e chave aleatória (UUID).
 */
class PixValidator
{
	/**
	 * Tipo da chave PIX identificado após validação
	 * 
	 * @var PixKeyTypeEnum|null
	 */
	public ?PixKeyTypeEnum $type = null;
	
	/**
	 * Status da validação
	 * 
	 * @var bool
	 */
	public bool $isValid = false;

	/**
	 * Cria uma nova instância do validador
	 * 
	 * @param string $chavePix Chave PIX a ser validada
	 */
	public function __construct(
		protected string $chavePix,
	) {
	}

	/**
	 * Valida a chave PIX fornecida
	 * 
	 * Percorre todos os tipos de chave PIX possíveis e verifica
	 * se a chave fornecida corresponde a algum dos formatos válidos.
	 * 
	 * @param bool $strict Se true, lança exceção em caso de chave inválida (padrão: true)
	 * @return bool True se a chave for válida, false caso contrário
	 * @throws PixException Se $strict for true e a chave for inválida
	 */
	public function validate(bool $strict = true): bool
	{
		foreach (PixKeyTypeEnum::cases() as $case) {
			if ($case->validate($this->chavePix)) {
				$this->type = $case;
				$this->isValid = true;
				return true;
			}
		}

		if ($strict) {
			throw new PixException('O formato da chave PIX é inválido');
		}

		return false;
	}
}

<?php

namespace VictorRenan\PixGenerator;

class PixValidator
{
	public ?PixKeyTypeEnum $type = null;
	public bool $isValid = false;

	public function __construct(
		protected string $chavePix,
	) {
	}

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

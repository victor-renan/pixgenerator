<?php

namespace VictorRenan\PixGenerator\Tests;

use PHPUnit\Framework\TestCase;
use VictorRenan\PixGenerator\PixValidator;
use VictorRenan\PixGenerator\PixException;
use VictorRenan\PixGenerator\PixKeyTypeEnum;

class PixValidatorTest extends TestCase
{
    public function testValidaCpf(): void
    {
        $validator = new PixValidator('12345678901');
        
        $this->assertTrue($validator->validate());
        $this->assertSame(PixKeyTypeEnum::CPF, $validator->type);
        $this->assertTrue($validator->isValid);
    }

    public function testValidaCnpj(): void
    {
        $validator = new PixValidator('12345678901234');
        
        $this->assertTrue($validator->validate());
        $this->assertSame(PixKeyTypeEnum::CNPJ, $validator->type);
        $this->assertTrue($validator->isValid);
    }

    public function testValidaPhone(): void
    {
        $validator = new PixValidator('+5511999999999');
        
        $this->assertTrue($validator->validate());
        $this->assertSame(PixKeyTypeEnum::PHONE, $validator->type);
        $this->assertTrue($validator->isValid);
    }

    public function testValidaUuid(): void
    {
        $validator = new PixValidator('f47ac10b-58cc-4372-a567-0e02b2c3d479');
        
        $this->assertTrue($validator->validate());
        $this->assertSame(PixKeyTypeEnum::UUID, $validator->type);
        $this->assertTrue($validator->isValid);
    }

    public function testValidaEmail(): void
    {
        $validator = new PixValidator('teste@exemplo.com');
        
        $this->assertTrue($validator->validate());
        $this->assertSame(PixKeyTypeEnum::EMAIL, $validator->type);
        $this->assertTrue($validator->isValid);
    }

    public function testErroSemStrict(): void
    {
        $validator = new PixValidator('invalido');
        
        $this->assertFalse($validator->validate(false));
        $this->assertNull($validator->type);
        $this->assertFalse($validator->isValid);
    }

    public function testErroComStrict(): void
    {
        $validator = new PixValidator('invalido');
        
        $this->expectException(PixException::class);
        $this->expectExceptionMessage('O formato da chave PIX é inválido');
        
        $validator->validate();
    }
}

<?php

namespace Tests\Unit;

use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;

class ValidateEmailRegTest extends TestCase
{
    public function testValidateEmailReg()
    {

        $superLongWord = Text::lexify(str_repeat("?", 257));
        $this->assertEquals("E-mail адрес слишком длинный", validateEmailReg($superLongWord, false));
        $this->assertEquals(null, validateEmailReg("hello@mail.ru", false));
        $this->assertEquals(null, validateEmailReg("    hello@mail.ru    ", false));
        $this->assertEquals("E-mail введён некорректно", validateEmailReg("fewfewfef", false));
        $this->assertEquals("E-mail введён некорректно", validateEmailReg("   ", false));
        $this->assertEquals(
            "Данный E-mail адрес уже занят",
            validateEmailReg("d_ivanov@mail.ru", true)
        );
        $this->assertEquals(
            "Данный E-mail адрес уже занят",
            validateEmailReg("a_beliy@mail.ru", true)
        );
    }
}

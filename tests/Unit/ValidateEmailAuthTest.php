<?php

namespace Tests\Unit;

use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;

class ValidateEmailAuthTest extends TestCase
{
    public function testValidateEmailAuth()
    {

        $superLongWord = Text::lexify(str_repeat("?", 257));
        $this->assertEquals(
            "E-mail адрес слишком длинный",
            validateEmailAuth($superLongWord, false)
        );
        $this->assertEquals(null, validateEmailAuth("hello@mail.ru", true));
        $this->assertEquals(null, validateEmailAuth("    hello@mail.ru    ", true));
        $this->assertEquals("E-mail введён некорректно", validateEmailAuth("fewfewfef", false));
        $this->assertEquals("E-mail введён некорректно", validateEmailAuth("   ", false));
        $this->assertEquals(
            "Пользователя с данным E-mail адресом не существует",
            validateEmailAuth("d_ivanov@mail.ru", false)
        );
        $this->assertEquals(
            "Пользователя с данным E-mail адресом не существует",
            validateEmailAuth("a_beliy@mail.ru", false)
        );
    }
}

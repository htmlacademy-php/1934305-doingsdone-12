<?php

namespace Tests\Unit;

use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;


class ValidateEmailTest extends TestCase
{
    public function testValidateEmail()
    {

        $superLongWord = Text::lexify(str_repeat("?", 257));
        $this->assertEquals("E-mail адрес слишком длинный", validateEmail($superLongWord, false));
        $this->assertEquals(null, validateEmail("hello@mail.ru", false));
        $this->assertEquals(null, validateEmail("    hello@mail.ru    ", false));
        $this->assertEquals("E-mail введён некорректно", validateEmail("fewfewfef", false));
        $this->assertEquals("E-mail введён некорректно", validateEmail("   ", false));
        $this->assertEquals("Данный E-mail адрес уже занят", validateEmail("d_ivanov@mail.ru", true));
        $this->assertEquals("Данный E-mail адрес уже занят", validateEmail("a_beliy@mail.ru", true));
    }
}


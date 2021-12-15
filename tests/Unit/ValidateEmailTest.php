<?php

namespace Tests\Unit;

use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;


class ValidateEmailTest extends TestCase
{
    public function testValidateEmail()
    {

        $superLongWord = Text::lexify(str_repeat("?", 257));
        $this->assertEquals("E-mail адрес слишком длинный", validateEmail($superLongWord, ["1@mail.ru"]));
        $this->assertEquals(null, validateEmail("hello@mail.ru", ["1@mail.ru"]));
        $this->assertEquals(null, validateEmail("    hello@mail.ru    ", ["1@mail.ru"]));
        $this->assertEquals("E-mail введён некорректно", validateEmail("fewfewfef", ["1@mail.ru"]));
        $this->assertEquals("E-mail введён некорректно", validateEmail("   ", ["1@mail.ru"]));
        $this->assertEquals("Данный E-mail адрес уже занят", validateEmail("d_ivanov@mail.ru", ["d_ivanov@mail.ru", "a_beliy@mail.ru"]));
        $this->assertEquals("Данный E-mail адрес уже занят", validateEmail("a_beliy@mail.ru", ["d_ivanov@mail.ru", "a_beliy@mail.ru"]));
    }
}


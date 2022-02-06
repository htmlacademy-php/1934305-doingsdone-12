<?php

namespace Tests\Unit;

use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;

class ValidateUserNameTest extends TestCase
{
    public function testValidateUserName()
    {
        $superLongWord = Text::lexify(str_repeat("?", 257));
        $this->assertEquals("Слишком длинное имя", validateUserName($superLongWord));
        $this->assertEquals("Введите имя пользователя", validateUserName(""));
        $this->assertEquals("Введите имя пользователя", validateUserName("       "));
        $this->assertEquals(null, validateUserName("Иван"));
        $this->assertEquals(null, validateUserName("Ivan"));
    }
}

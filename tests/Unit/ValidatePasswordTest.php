<?php

namespace Tests\Unit;

use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;


class ValidatePasswordTest extends TestCase
{
    public function testValidatePassword()
    {
        $superLongPassword= Text::lexify(str_repeat("?", 257));
        $passwordWithWhiteSpace = "hello world";
        $passwordWithLessEightChars = "hahahah";
        $whiteSpacedPassword = "     helloWorld     ";
        $acceptedPassword = "HelloWorld";
        $this->assertEquals("Пароль не должен превышать лимит 60-ти символов", validatePassword($superLongPassword));
        $this->assertEquals("Пароль не должен содержать пробельные символы", validatePassword($passwordWithWhiteSpace));
        $this->assertEquals("Пароль должен содержать минимум 8 символов", validatePassword($passwordWithLessEightChars));
        $this->assertEquals("Пароль не должен содержать пробельные символы", validatePassword($whiteSpacedPassword));
        $this->assertEquals(null, validatePassword($acceptedPassword));
    }
}

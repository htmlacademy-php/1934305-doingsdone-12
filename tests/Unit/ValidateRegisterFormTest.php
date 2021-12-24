<?php

namespace Tests\Unit;

use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;


class ValidateRegisterFormTest extends TestCase
{
    public function testValidateRegisterForm()
    {
        $superLongName = Text::lexify(str_repeat("?", 257));
        $superLongPassword = Text::lexify(str_repeat("?", 70));
        $superLongEmail = Text::lexify(str_repeat("?", 257)) . "@mail.ru";

        $expected = ["email" => "E-mail адрес слишком длинный", "password" => "Пароль не должен превышать лимит 60-ти символов", "name" => "Слишком длинное имя"];
        $testRegisterForm = ["email" => $superLongEmail, "password" => $superLongPassword, "name" => $superLongName];
        $this->assertEquals($expected, validateRegisterForm($testRegisterForm, $con));

        $emptyUserName = "    ";
        $emptyPassword = "   ";
        $emptyEmail = "   ";
        $expected = ["email" => "E-mail введён некорректно", "password" => "Пароль не должен содержать пробельные символы", "name" => "Введите имя пользователя"];
        $testRegisterForm = ["email" => $emptyEmail, "password" => $emptyPassword, "name" => $emptyUserName];
        $this->assertEquals($expected, validateRegisterForm($testRegisterForm, $con));

        $emptyUserName = "    ";
        $wrongEmail = Text::lexify(str_repeat("?", 10));
        $wrongPassword = Text::lexify(str_repeat("?", 5)) . "  " . Text::lexify(str_repeat("?", 6));
        $expected = ["email" => "E-mail введён некорректно", "password" => "Пароль не должен содержать пробельные символы", "name" => "Введите имя пользователя"];
        $testRegisterForm = ["email" => $wrongEmail, "password" => $wrongPassword, "name" => $emptyUserName];
        $this->assertEquals($expected, validateRegisterForm($testRegisterForm, $con));

        $emptyUserName = "    ";
        $usedEmail = "d_ivanov@mail.ru";
        $wrongPassword = Text::lexify(str_repeat("?", 5)) . "  " . Text::lexify(str_repeat("?", 6));
        $expected = ["email" => "Данный E-mail адрес уже занят", "password" => "Пароль не должен содержать пробельные символы", "name" => "Введите имя пользователя"];
        $testRegisterForm = ["email" => $usedEmail, "password" => $wrongPassword, "name" => $emptyUserName];
        $this->assertEquals($expected, validateRegisterForm($testRegisterForm, $con));

        $userName = "Виктор";
        $email = "hello@mail.ru";
        $password = "123456789";
        $expected = ["email" => null, "password" => null, "name" => null];
        $testRegisterForm = ["email" => $email, "password" => $password, "name" => $userName];
        $this->assertEquals($expected, validateRegisterForm($testRegisterForm, $con));
    }
}

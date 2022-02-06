<?php

namespace Tests\Unit;

use Database;
use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;

class ValidateRegisterFormTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $user = ["name" => "Дима", "password" => "12345678", "email" => "d_ivanov@mail.ru"];
        createNewUser(Database::$con, $user);
    }


    public function testValidateRegisterForm()
    {
        $superLongName = Text::lexify(str_repeat("?", 257));
        $superLongPassword = Text::lexify(str_repeat("?", 70));
        $superLongEmail = Text::lexify(str_repeat("?", 257)) . "@mail.ru";

        $expected = [
            "email" => "E-mail адрес слишком длинный",
            "password" => "Пароль не должен превышать лимит 60-ти символов",
            "name" => "Слишком длинное имя"
        ];
        $testRegisterForm = ["email" => $superLongEmail, "password" => $superLongPassword, "name" => $superLongName];
        $this->assertEquals($expected, validateRegisterForm($testRegisterForm, Database::$con));

        $emptyUserName = "    ";
        $emptyPassword = "   ";
        $emptyEmail = "   ";
        $expected = [
            "email" => "E-mail введён некорректно",
            "password" => "Пароль не должен содержать пробельные символы",
            "name" => "Введите имя пользователя"
        ];
        $testRegisterForm = ["email" => $emptyEmail, "password" => $emptyPassword, "name" => $emptyUserName];
        $this->assertEquals($expected, validateRegisterForm($testRegisterForm, Database::$con));

        $wrongEmail = Text::lexify(str_repeat("?", 10));
        $wrongPassword = Text::lexify(str_repeat("?", 5)) . "  " . Text::lexify(str_repeat("?", 6));

        $testRegisterForm = ["email" => $wrongEmail, "password" => $wrongPassword, "name" => $emptyUserName];
        $this->assertEquals($expected, validateRegisterForm($testRegisterForm, Database::$con));

        $usedEmail = "d_ivanov@mail.ru";
        $wrongPassword = Text::lexify(str_repeat("?", 5)) . "  " . Text::lexify(str_repeat("?", 6));
        $expected = [
            "email" => "Данный E-mail адрес уже занят",
            "password" => "Пароль не должен содержать пробельные символы",
            "name" => "Введите имя пользователя"
        ];
        $testRegisterForm = ["email" => $usedEmail, "password" => $wrongPassword, "name" => $emptyUserName];
        $this->assertEquals($expected, validateRegisterForm($testRegisterForm, Database::$con));

        $userName = "Виктор";
        $email = "hello@mail.ru";
        $password = "123456789";
        $expected = [];
        $testRegisterForm = ["email" => $email, "password" => $password, "name" => $userName];
        $this->assertEquals($expected, validateRegisterForm($testRegisterForm, Database::$con));
    }

    public function tearDown(): void
    {
        parent::tearDown();

        mysqli_query(Database::$con, "SET foreign_key_checks = 0");
        mysqli_query(Database::$con, "TRUNCATE users");
        mysqli_query(Database::$con, "SET foreign_key_checks = 1");
    }
}

<?php

namespace Tests\Unit;

use Database;
use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;

class ValidateAuthFormTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        createNewUser(Database::$con, ["name" => "Вася", "password" => "12345678", "email" => "ddd@mail.ru"]);
        createNewUser(Database::$con, ["name" => "Вася", "password" => "12345678", "email" => "ddd1@mail.ru"]);
        createNewUser(Database::$con, ["name" => "Вася", "password" => "12345678", "email" => "ddd2@mail.ru"]);
        createNewUser(Database::$con, ["name" => "Вася", "password" => "12345678", "email" => "ddd3@mail.ru"]);
    }

    public function testValidateAuthForm()
    {
        $expected = [
            "email" => "Пользователя с данным E-mail адресом не существует",
            "password" => "Пароль не должен превышать лимит 60-ти символов"
        ];
        $superLongPassword = Text::lexify(str_repeat("?", 257));
        $authFormTest = ["email" => "d_ivanov@mail.ru", "password" => $superLongPassword];
        $this->assertEquals($expected, validateAuthForm($authFormTest, Database::$con));

        $expected = [
            "email" => "E-mail введён некорректно",
            "password" => "Пароль не должен содержать пробельные символы"
        ];
        $passwordWithWhiteSpace = "hello world";
        $authFormTest = ["email" => "fewfewfef", "password" => $passwordWithWhiteSpace];
        $this->assertEquals($expected, validateAuthForm($authFormTest, Database::$con));

        $expected = [
            "email" => "E-mail адрес слишком длинный",
            "password" => "Пароль должен содержать минимум 8 символов"
        ];
        $passwordWithLessEightChars = "hahahah";
        $superLongWord = Text::lexify(str_repeat("?", 257));
        $authFormTest = ["email" => $superLongWord, "password" => $passwordWithLessEightChars];
        $this->assertEquals($expected, validateAuthForm($authFormTest, Database::$con));

        $expected = [];
        $acceptedPassword = "HelloWorld";
        $acceptedEmail = "ddd@mail.ru";
        $authFormTest = ["email" => $acceptedEmail, "password" => $acceptedPassword];
        $this->assertEquals($expected, validateAuthForm($authFormTest, Database::$con));
    }

    public function tearDown(): void
    {
        parent::tearDown();

        mysqli_query(Database::$con, "SET foreign_key_checks = 0");
        mysqli_query(Database::$con, "TRUNCATE projects");
        mysqli_query(Database::$con, "TRUNCATE users");
        mysqli_query(Database::$con, "TRUNCATE tasks");
        mysqli_query(Database::$con, "SET foreign_key_checks = 1");
    }
}

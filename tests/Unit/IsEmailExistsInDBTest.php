<?php

namespace Tests\Unit;

use Database;
use PHPUnit\Framework\TestCase;

class IsEmailExistsInDBTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        createNewUser(Database::$con, ["name" => "Вася", "password" => "12345678", "email" => "ddd@mail.ru"]);
        createNewUser(Database::$con, ["name" => "Вася", "password" => "12345678", "email" => "ddd1@mail.ru"]);
        createNewUser(Database::$con, ["name" => "Вася", "password" => "12345678", "email" => "ddd2@mail.ru"]);
        createNewUser(Database::$con, ["name" => "Вася", "password" => "12345678", "email" => "ddd3@mail.ru"]);

    }

    public function testIsEmailExistsInDB()
    {
        $this->assertEquals(false, isEmailExistsInDB(Database::$con, "hello@mail.ru"));
        $this->assertEquals(true, isEmailExistsInDB(Database::$con, "ddd@mail.ru"));
        $this->assertEquals(true, isEmailExistsInDB(Database::$con, "ddd1@mail.ru"));
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

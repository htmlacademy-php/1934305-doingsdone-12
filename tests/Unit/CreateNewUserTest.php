<?php

namespace Tests\Unit;

use Database;
use PHPUnit\Framework\TestCase;

class CreateNewUserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateNewUser()
    {
        $user = ["name" => "Вася", "password" => "12345678", "email" => "ddd@mail.ru"];
        $this->assertEquals(true, createNewUser(Database::$con, $user));

        // теперь оно должно вернуть false из-за уникальности поля email в БД
        $this->assertEquals(false, createNewUser(Database::$con, $user));
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

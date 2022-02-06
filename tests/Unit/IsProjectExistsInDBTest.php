<?php

namespace Tests\Unit;

use Database;
use PHPUnit\Framework\TestCase;

class IsProjectExistsInDBTest extends TestCase
{
    public int $userId;
    public function setUp(): void
    {
        parent::setUp();

        createNewUser(Database::$con, ["name" => "Вася", "password" => "12345678", "email" => "ddd@mail.ru"]);

        $this->userId = mysqli_insert_id(Database::$con);

        mysqli_query(
            Database::$con,
            "INSERT INTO projects(name, user_id)
                        VALUES (\"Входящие\", {$this->userId}),
                            (\"Учеба\", {$this->userId}),
                            (\"Работа\", {$this->userId}),
                            (\"Домашние дела\", {$this->userId}),
                            (\"Авто\", {$this->userId});"
        );
    }

    public function testIsProjectExistsInDB()
    {
        $this->assertEquals(false, isProjectExistsInDB(Database::$con, "Входящие", 2));
        $this->assertEquals(true, isProjectExistsInDB(Database::$con, "Входящие", $this->userId));
        $this->assertEquals(false, isProjectExistsInDB(Database::$con, "Новые дела", $this->userId));
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

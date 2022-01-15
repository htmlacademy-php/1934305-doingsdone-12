<?php

namespace Tests\Unit;

use Database;
use PHPUnit\Framework\TestCase;

class UpdateStatusTaskTest extends TestCase
{
    private int $userId;

    public function setUp(): void
    {
        parent::setUp();

        $user = ["name" => "Вася", "password" => "12345678", "email" => "ddd@mail.ru"];
        createNewUser(Database::$con, $user);

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

        $taskThresholds = ["name" => "task1", "project_id" => 1, "end_time" => "2021-1-1", "user_id" => $this->userId, "file" => ""];

        for ($i = 0; $i < 12; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }

    }

    public function testUpdateStatusTask()
    {
        $this->assertEquals(true, updateStatusTask(Database::$con, $this->userId, 3));
        $this->assertEquals(false, updateStatusTask(Database::$con, $this->userId, 15));
        $this->assertEquals(false, updateStatusTask(Database::$con, 3, 4));
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

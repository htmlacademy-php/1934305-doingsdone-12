<?php

namespace Tests\Unit;

use Database;
use PHPUnit\Framework\TestCase;

class GetTasksByProjectIdTest extends TestCase
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

        for ($i = 0; $i < 6; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }

        $taskThresholds["project_id"] = 2;

        for ($i = 0; $i < 6; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }

    }

    public function testGetTasksByProjectId()
    {
        $this->assertEquals([], getTasksByProjectId(Database::$con, 666, 2));

        $expected1 = json_decode(file_get_contents(__DIR__ . "/../data/tasks-by-projects-id-1.json"), true);
        $this->assertEquals($expected1, getTasksByProjectId(Database::$con, $this->userId, 1));

        $expected2 = json_decode(file_get_contents(__DIR__ . "/../data/tasks-by-projects-id-2.json"), true);
        $this->assertEquals($expected2, getTasksByProjectId(Database::$con, $this->userId, 2));

        $this->assertEquals([], getTasksByProjectId(Database::$con, $this->userId, 4));
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

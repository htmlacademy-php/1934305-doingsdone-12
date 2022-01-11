<?php

namespace Tests\Unit;

use Database;
use PHPUnit\Framework\TestCase;

class CreateNewTaskTest extends TestCase
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

    }

    public function testCreateNewTask()
    {
        $taskThresholds = ["name" => "task1", "project_id" => 1, "end_time" => "2021-1-1", "user_id" => $this->userId, "file" => ""];
        $emptyTasks = ["name" => null, "project_id" => null, "end_time" => null, "user_id" => null, "file" => null];
        $emptyNameTask = ["name" => null, "project_id" => 1, "end_time" => "2021-1-1", "user_id" => $this->userId, "file" => ""];
        $emptyProjectIdTask = ["name" => "task1", "project_id" => null, "end_time" => "2021-1-1", "user_id" => $this->userId, "file" => ""];
        $emptyUserIdTask = ["name" => "task1", "project_id" => 1, "end_time" => "2021-1-1", "user_id" => null, "file" => ""];

        $this->assertEquals(true, createNewTask(Database::$con, $taskThresholds));
        $this->assertEquals(false, createNewTask(Database::$con, $emptyTasks));
        $this->assertEquals(false, createNewTask(Database::$con, $emptyNameTask));
        $this->assertEquals(false, createNewTask(Database::$con, $emptyProjectIdTask));
        $this->assertEquals(false, createNewTask(Database::$con, $emptyUserIdTask));
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

<?php

namespace Tests\Unit;

use Database;
use PHPUnit\Framework\TestCase;

class GetUsersTasksByDateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $user = ["name" => "Вася", "password" => "12345678", "email" => "ddd@mail.ru"];
        createNewUser(Database::$con, $user);
        $user = ["name" => "Петя", "password" => "12345678", "email" => "ddd1@mail.ru"];
        createNewUser(Database::$con, $user);
        $user = ["name" => "Саша", "password" => "12345678", "email" => "ddd3@mail.ru"];
        createNewUser(Database::$con, $user);
        $user = ["name" => "Саша", "password" => "12345678", "email" => "ddd4@mail.ru"];
        createNewUser(Database::$con, $user);

        mysqli_query(
            Database::$con,
            "INSERT INTO projects(name, user_id)
                        VALUES (\"Входящие\", 1)"
        );

        mysqli_query(
            Database::$con,
            "INSERT INTO projects(name, user_id)
                        VALUES (\"Входящие\", 2)"
        );

        mysqli_query(
            Database::$con,
            "INSERT INTO projects(name, user_id)
                        VALUES (\"Входящие\", 3)"
        );


        mysqli_query(
            Database::$con,
            "INSERT INTO projects(name, user_id)
                        VALUES (\"Входящие\", 4)"
        );

        $taskThresholds = [
            "name" => "task1",
            "project_id" => 1,
            "end_time" => "2021-1-1",
            "user_id" => 1,
            "file" => ""
        ];

        for ($i = 0; $i < 6; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }

        $taskThresholds = [
                "name" => "task1",
                "project_id" => 1,
                "end_time" => "2021-1-4",
                "user_id" => 1,
                "file" => ""
        ];

        for ($i = 0; $i < 6; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }

        $taskThresholds = [
                "name" => "task1",
                "project_id" => 2,
                "end_time" => "2021-1-1",
                "user_id" => 2,
                "file" => ""
        ];

        for ($i = 0; $i < 6; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }

        $taskThresholds = [
                "name" => "task1",
                "project_id" => 2,
                "end_time" => "2021-1-4",
                "user_id" => 2,
                "file" => ""
        ];

        for ($i = 0; $i < 6; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }

        $taskThresholds = [
                "name" => "task1",
                "project_id" => 3,
                "end_time" => "2021-1-1",
                "user_id" => 3,
                "file" => ""
        ];

        for ($i = 0; $i < 6; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }

        $taskThresholds = [
                "name" => "task1",
                "project_id" => 3,
                "end_time" => "2021-1-4",
                "user_id" => 3,
                "file" => ""
        ];

        for ($i = 0; $i < 6; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }

        $taskThresholds = [
                "name" => "task1",
                "project_id" => 4,
                "end_time" => "2021-1-1",
                "user_id" => 4,
                "file" => ""
        ];

        for ($i = 0; $i < 6; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }

        $taskThresholds = [
                "name" => "task1",
                "project_id" => 4,
                "end_time" => "2021-1-4",
                "user_id" => 4,
                "file" => ""
        ];

        for ($i = 0; $i < 6; $i++) {
            createNewTask(Database::$con, $taskThresholds);
        }
    }

    public function testGetUsersTasksByDate()
    {
        $this->assertEquals([], getUsersTasksByDate(Database::$con, date_create()->format("Y-m-d")));

        $expected = json_decode(file_get_contents(__DIR__ . "/../data/users-tasks-by-date.json"), true);
        $this->assertEquals($expected, getUsersTasksByDate(Database::$con, date_create("2021-1-4")->format("Y-m-d")));
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

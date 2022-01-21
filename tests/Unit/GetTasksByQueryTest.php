<?php

namespace Tests\Unit;

use Database;
use PHPUnit\Framework\TestCase;

class GetTasksByQueryTest extends TestCase
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

        $task1 = [
            "name" => "Поесть пиццу",
            "project_id" => 1,
            "end_time" => "2021-1-1",
            "user_id" => $this->userId,
            "file" => ""
        ];
        $task2 = [
            "name" => "Поесть кальмаров",
            "project_id" => 1,
            "end_time" => "2021-1-1",
            "user_id" => $this->userId,
            "file" => ""
        ];
        $task3 = [
            "name" => "Погулять в деревне",
            "project_id" => 1,
            "end_time" => "2021-1-1",
            "user_id" => $this->userId,
            "file" => ""
        ];
        $task4 = [
            "name" => "Погулять по городу",
            "project_id" => 1,
            "end_time" => "2021-1-1",
            "user_id" => $this->userId,
            "file" => ""
        ];
        createNewTask(Database::$con, $task1);
        createNewTask(Database::$con, $task2);
        createNewTask(Database::$con, $task3);
        createNewTask(Database::$con, $task4);
    }

    public function testGetTasksByQueryTest()
    {
        $this->assertEquals(null, getTasksByQuery(Database::$con, $this->userId, "поехать"));

        $expected = json_decode(file_get_contents(__DIR__ . "/../data/tasks-by-query-1.json"), true);
        $this->assertEquals($expected, getTasksByQuery(Database::$con, $this->userId, "Поесть"));

        $expected = json_decode(file_get_contents(__DIR__ . "/../data/tasks-by-query-2.json"), true);
        $this->assertEquals($expected, getTasksByQuery(Database::$con, $this->userId, "Погулять"));

        $expected = json_decode(file_get_contents(__DIR__ . "/../data/tasks-by-query-1.json"), true);
        $this->assertEquals($expected, getTasksByQuery(Database::$con, $this->userId, "поесть"));

        $expected = json_decode(file_get_contents(__DIR__ . "/../data/tasks-by-query-2.json"), true);
        $this->assertEquals($expected, getTasksByQuery(Database::$con, $this->userId, "погулять"));
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

<?php

namespace Tests\Unit;

use Database;
use PHPUnit\Framework\TestCase;

class CreateNewProjectTest extends TestCase
{
    public int $userId;
    public function setUp(): void
    {
        parent::setUp();
        $user = ["name" => "Вася", "password" => "12345678", "email" => "ddd@mail.ru"];
        createNewUser(Database::$con, $user);
        $this->userId = mysqli_insert_id(Database::$con);
    }

    public function testCreateNewProject()
    {
        $newProject = ["project_name" => "Входящие", "user_id" => $this->userId];
        $this->assertEquals(true, createNewProject(Database::$con, $newProject));

        $falseProject = ["project_name" => "Входящие", "user_id" => 2];
        $this->assertEquals(false, createNewProject(Database::$con, $falseProject));
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

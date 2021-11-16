<?php

namespace Unit;

use PHPUnit\Framework\TestCase;

class CountProjectsTest extends TestCase
{
    public function testCountProjects()
    {
        $tasks = [
            [
                "task_name" => "Собеседование в IT компании",
                "date" => "01.12.2019",
                "project" => "Работа",
                "is_finished" => false
            ],

            [
                "task_name" => "Выполнить тестовое задание",
                "date" => "25.12.2019",
                "project" => "Работа",
                "is_finished" => false
            ],

            [
                "task_name" => "Сделать задание первого раздела",
                "date" => "21.12.2019",
                "project" => "Учеба",
                "is_finished" => true
            ],

            [
                "task_name" => "Встреча с другом",
                "date" => "22.12.2019",
                "project" => "Входящие",
                "is_finished" => false
            ],

            [
                "task_name" => "Купить корм для кота",
                "date" => null,
                "project" => "Домашние дела",
                "is_finished" => false
            ],

            [
                "task_name" => "Заказать пиццу",
                "date" => null,
                "project" => "Домашние дела",
                "is_finished" => false
            ],

        ];

        $this->assertEquals(2, countProjects("Домашние дела", $tasks));
        $this->assertEquals(1, countProjects("Входящие", $tasks));
        $this->assertEquals(2, countProjects("Работа", $tasks));
        $this->assertEquals(1, countProjects("Учеба", $tasks));
        $this->assertEquals(0, countProjects("Спорт", $tasks));
    }
}

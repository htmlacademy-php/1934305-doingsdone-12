<?php

namespace Tests\Unit;

use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;

class ValidateTaskFormTest extends TestCase
{
    public function testValidateTaskForm()
    {
        $expected = [
            "project_id" => "Указан несуществующий проект",
            "name" => "Название не должно превышать размер в 255 символов",
            "end_time" => "Неверный формат даты"
        ];
        $superLongWord = Text::lexify(str_repeat("?", 257));
        $testTaskFormData = ["project_id" => "", "name" => $superLongWord, "end_time" => "2021.10.10"];
        $this->assertEquals($expected, validateTaskForm($testTaskFormData, [1, 2, 3], date_create()->format("Y-m-d")));


        $expected = [
            "name" => "Поле название надо заполнить",
            "end_time" =>
                "Выбранная дата должна быть больше или равна текущей"
        ];
        $onlySpacesWord = "    ";
        $testTaskFormData = ["project_id" => 1, "name" => $onlySpacesWord, "end_time" => "2021-10-10"];
        $this->assertEquals($expected, validateTaskForm($testTaskFormData, [1, 2, 3], "2021-10-12"));

        $expected = [];
        $shortWord = Text::lexify(str_repeat("?", 30));
        $testTaskFormData = ["project_id" => 2, "name" => $shortWord, "end_time" => ""];
        $this->assertEquals($expected, validateTaskForm($testTaskFormData, [1, 2, 3], "2021-10-12"));
    }
}

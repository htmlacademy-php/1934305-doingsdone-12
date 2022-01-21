<?php

namespace Tests\Unit;

use Faker\Provider\ru_RU\Text;
use PHPUnit\Framework\TestCase;

class ValidateTaskNameTest extends TestCase
{
    public function testValidateTaskName()
    {

        $superLongWord = Text::lexify(str_repeat("?", 257));
        $onlySpacesWord = "    ";
        $shortWord = Text::lexify(str_repeat("?", 30));
        $shortWithSpacesWord = "   " . Text::lexify(str_repeat("?", 30)) . "   ";

        $this->assertEquals(
            "Название не должно превышать размер в 255 символов",
            validateTaskName($superLongWord)
        );
        $this->assertEquals("Поле название надо заполнить", validateTaskName($onlySpacesWord));
        $this->assertEquals(null, validateTaskName($shortWord));
        $this->assertEquals(null, validateTaskName($shortWithSpacesWord));
    }
}

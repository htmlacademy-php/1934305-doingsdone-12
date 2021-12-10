<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ValidateDateTest extends TestCase
{
    public function testValidateDate()
    {
        $this->assertEquals(null, validateDate("", date_create()->format("Y-m-d")));
        $this->assertEquals("Неверный формат даты", validateDate("2021.10.10", date_create()->format("Y-m-d")));
        $this->assertEquals(
            "Выбранная дата должна быть больше или равна текущей",
            validateDate("2021-10-10", "2021-10-12")
        );
        $this->assertEquals(null, validateDate("2021-12-12", "2021-12-03"));
        $this->assertEquals(null, validateDate("2022-01-12", "2021-01-12"));
    }
}

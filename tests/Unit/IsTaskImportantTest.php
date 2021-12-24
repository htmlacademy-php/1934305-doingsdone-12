<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class IsTaskImportantTest extends TestCase
{
    public function testIsTaskImportant()
    {
        $this->assertEquals(true, isTaskImportant("2021-01-01", date_create("2021-01-01")));
        $this->assertEquals(true, isTaskImportant("2021-01-01", date_create("2021-01-02")));
        $this->assertEquals(false, isTaskImportant("2021-01-02", date_create("2021-01-01")));
        $this->assertEquals(false, isTaskImportant("2021-12-31", date_create("2021-12-01")));
    }
}

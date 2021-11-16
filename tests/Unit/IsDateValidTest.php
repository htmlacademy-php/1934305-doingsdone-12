<?php

namespace Unit;

use PHPUnit\Framework\TestCase;

class IsDateValidTest extends TestCase
{
    public function testIsDateValid()
    {
        $this->assertEquals(true, isDateValid("2019-01-01"));
        $this->assertEquals(true, isDateValid("2016-02-29"));
        $this->assertEquals(false, isDateValid("2019-04-31"));
        $this->assertEquals(false, isDateValid("10.10.2010"));
        $this->assertEquals(false, isDateValid("10/10/2010"));
    }
}

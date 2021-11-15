<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once "func.php";

class ExampleTest extends TestCase
{
    public function testExample()
    {
        $this->assertEquals("HELLO", makeUppercase("hello"));
        $this->assertEquals("ПРИВЕТ", makeUppercase("привет"));
    }
}

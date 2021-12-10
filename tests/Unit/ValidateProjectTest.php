<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ValidateProjectTest extends TestCase
{
    public function testValidateProject()
    {
        $this->assertEquals(null, validateProject(1, [1, 2, 3, 4]));
        $this->assertEquals("Указан несуществующий проект", validateProject(0, [1, 2]));
    }
}

<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MakeURLTest extends TestCase
{
    public function testMakeURL()
    {
        $this->assertEquals("/index.php?tab=top&sort=desc&update=1", makeURL("index.php", [
            "tab" => "top",
            "sort" => "desc",
            "update" => 1
        ]));

        $this->assertEquals("/MakeURLTest.php?id=1", makeURL(pathinfo(__FILE__, PATHINFO_BASENAME), [
            "id" => 1
        ]));

        $this->assertEquals("/index.php", makeURL("index.php", []));
    }
}

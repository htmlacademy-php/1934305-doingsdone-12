<?php
/* @var mysqli $con
*/

require_once "init.php";

$title = "Дела в порядке";

$pageContent = includeTemplate("register-form.php", []);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
    "addScript" => pathinfo("add.php", PATHINFO_BASENAME)
]);

print($layoutContent);

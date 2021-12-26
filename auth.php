<?php
/* @var string $title
*/
require_once "init.php";

$errors = [];

$pageContent = includeTemplate("form-authorization.php", [
    "authScript" => pathinfo("auth.php", PATHINFO_BASENAME),
    "errors" => $errors
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
    "authScript" => pathinfo("auth.php", PATHINFO_BASENAME)
]);

print($layoutContent);

<?php

require_once("init.php");

$showCompleteTasks = rand(0, 1);
$title = "Дела в порядке";

$con = makeConnection($config["db"]);

$id = 1; // Сейчас пока 1, потом заменю на $_GET
$projects = [];

$projects = getProjects($con, $id);
$tasks = getTasks($con, $id);

$pageContent = includeTemplate("main.php", [
    "projects" => $projects,
    "tasks" => $tasks,
    "showCompleteTasks" => $showCompleteTasks,
    "scriptName" => pathinfo(__FILE__, PATHINFO_BASENAME)
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title
]);

print($layoutContent);

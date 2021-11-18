<?php

require_once("init.php");

$showCompleteTasks = 1;
$title = "Дела в порядке";

$con = makeConnection($config["db"]);

$userId = 1; // Сейчас пока 1, потом заменю на $_GET
$projectId = $_GET["project_id"] ?? null;

$projects = [];

$projects = getProjects($con, $userId);

if ($projectId) {
    $tasks = getTasksByProjectId($con, $userId, $projectId);
} else {
    $tasks = getTasksAll($con, $userId);
}

$pageContent = includeTemplate("main.php", [
    "projects" => $projects,
    "tasks" => $tasks,
    "showCompleteTasks" => $showCompleteTasks,
    "scriptName" => pathinfo(__FILE__, PATHINFO_BASENAME),
    "projectId" => $projectId
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title
]);

print($layoutContent);

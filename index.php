<?php

require_once("init.php");

$showCompleteTasks = 1;
$title = "Дела в порядке";

$con = makeConnection($config["db"]);

$userId = 1; // Сейчас пока 1, потом заменю на $_GET
$projectId = filter_input(INPUT_GET, "project_id", FILTER_SANITIZE_NUMBER_INT);

$projects = [];

$projects = getProjects($con, $userId);

if ($projectId) {
    $tasks = getTasksByProjectId($con, $userId, $projectId);
} else {
    $tasks = getTasksAll($con, $userId);
}

$isProjectExist = in_array($projectId, array_column($projects, "id"));

if ($projectId !== null && $isProjectExist === false) {
    http_response_code(404);
    exit();
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

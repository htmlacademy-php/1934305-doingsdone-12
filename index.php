<?php

require_once("init.php");

$showCompleteTasks = 1;
$title = "Дела в порядке";

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

$projectsSide = includeTemplate("projects-side.php", [
    "projects" => $projects,
    "scriptName" => pathinfo(__FILE__, PATHINFO_BASENAME),
    "projectId" => $projectId
]);

$pageContent = includeTemplate("main.php", [
    "projectsSide" => $projectsSide,
    "tasks" => $tasks,
    "showCompleteTasks" => $showCompleteTasks,
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
    "addScript" => pathinfo("add.php", PATHINFO_BASENAME)
]);

print($layoutContent);

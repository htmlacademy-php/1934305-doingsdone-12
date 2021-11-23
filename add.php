<?php
require_once("init.php");

$title = "Дела в порядке";

$userId = 1;
$projectId = filter_input(INPUT_GET, "project_id", FILTER_SANITIZE_NUMBER_INT);
$projects = getProjects($con, $userId);

$projectsSide = includeTemplate("projects-side.php", [
    "projects" => $projects,
    "scriptName" => pathinfo("index.php", PATHINFO_BASENAME),
    "projectId" => $projectId
]);

$pageContent = includeTemplate("form-task.php", [
    "projectsSide" => $projectsSide
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
    "addScript" => pathinfo("add.php", PATHINFO_BASENAME)
]);

print($layoutContent);

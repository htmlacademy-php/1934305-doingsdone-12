<?php

/* @var mysqli $con
 * @var string $title
 * @var int $userId
 */

require_once "init.php";

if (!isset($_SESSION["user"])) {
    $pageContent = includeTemplate("guest.php");

    $layoutContent = includeTemplate("layout.php", [
        "content" => $pageContent,
        "title" => $title,
    ]);

    print($layoutContent);
    exit();
}

saveCompleteTasksToSession();
$showCompleteTasks = (int) $_SESSION["show_complete_tasks"];

$queryStringsValues = getQueriesWrapper();

if ($queryStringsValues["task_id"]) {
    updateStatusTask($con, $userId, $queryStringsValues["task_id"]);
}

$projects = [];

$projects = getProjects($con, $userId);

$tasks = getTasksWrapper($con, $userId, $queryStringsValues, $showCompleteTasks);

$isProjectExist = in_array($queryStringsValues["project_id"], array_column($projects, "id"));

if ($queryStringsValues["project_id"] && $isProjectExist === false) {
    http_response_code(404);
    exit();
}

$projectsSideTemplate = includeTemplate("projects-side.php", [
    "projects" => $projects,
    "scriptName" => pathinfo(__FILE__, PATHINFO_BASENAME),
    "projectId" => $queryStringsValues["project_id"]
]);

$pageContent = includeTemplate("main.php", [
    "projectsSideTemplate" => $projectsSideTemplate,
    "tasks" => $tasks,
    "showCompleteTasks" => $showCompleteTasks,
    "scriptName" => pathinfo(__FILE__, PATHINFO_BASENAME),
    "btnActive" => $queryStringsValues
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
]);

print($layoutContent);

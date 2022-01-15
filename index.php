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

if (!isset($_SESSION["show_complete_tasks"])) {
    $_SESSION["show_complete_tasks"] = 1;
}

if (isset($_GET["show_completed"])) {
    $_SESSION["show_complete_tasks"] = filter_input(INPUT_GET, "show_completed", FILTER_SANITIZE_NUMBER_INT);
}

$showCompleteTasks = (int) $_SESSION["show_complete_tasks"];

$projectId = filter_input(INPUT_GET, "project_id", FILTER_SANITIZE_NUMBER_INT);

$taskId = filter_input(INPUT_GET, "task_id", FILTER_SANITIZE_NUMBER_INT);

if ($taskId) {
    updateStatusTask($con, $userId, $taskId);
}

$projects = [];

$projects = getProjects($con, $userId);

if ($projectId) {
    $tasks = getTasksByProjectId($con, $userId, $projectId);
} elseif (isset($_GET["query"])) {
    $query = filter_input(INPUT_GET, "query", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $tasks = getTasksByQuery($con, $userId, $query);
} else {
    $tasks = getTasksAll($con, $userId);
}

$isProjectExist = in_array($projectId, array_column($projects, "id"));

if ($projectId !== null && $isProjectExist === false) {
    http_response_code(404);
    exit();
}

$projectsSideTemplate = includeTemplate("projects-side.php", [
    "projects" => $projects,
    "scriptName" => pathinfo(__FILE__, PATHINFO_BASENAME),
    "projectId" => $projectId
]);

$pageContent = includeTemplate("main.php", [
    "projectsSideTemplate" => $projectsSideTemplate,
    "tasks" => $tasks,
    "showCompleteTasks" => $showCompleteTasks,
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
]);

print($layoutContent);

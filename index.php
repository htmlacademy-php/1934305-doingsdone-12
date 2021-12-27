<?php
/* @var mysqli $con
 * @var string $title
 */

require_once "init.php";

if (empty($_SESSION)) {

    $pageContent = includeTemplate("guest.php", [
        "registerScript" => pathinfo("register.php", PATHINFO_BASENAME)
    ]);

    $layoutContent = includeTemplate("layout.php", [
        "content" => $pageContent,
        "title" => $title,
        "authScript" => pathinfo("auth.php", PATHINFO_BASENAME)
    ]);

    print($layoutContent);
    exit();
}

$showCompleteTasks = 1;

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
    "addScript" => pathinfo("add.php", PATHINFO_BASENAME),
    "logoutScript" => pathinfo("logout.php", PATHINFO_BASENAME)
]);

print($layoutContent);

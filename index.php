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
        "bodyBackground" => "body-background"
    ]);

    print($layoutContent);
    exit();
}

$errors = [];

saveCompleteTasksToSession();
$showCompleteTasks = (int) $_SESSION["show_complete_tasks"];

$queryStringsValues = getQueriesWrapper();
$criteria = makeCriteria($queryStringsValues);

if ($criteria[TASK_ID]) {
    updateStatusTask($con, $userId, $criteria[TASK_ID]);
}

if (isset($_GET[QUERY])) {
    $errors = validateSearchQueryForm($_GET[QUERY]);
}

$projects = [];

$projects = getProjects($con, $userId);

$tasks = getTasksWrapper($con, $userId, $criteria, $showCompleteTasks, $errors);

$isProjectExist = in_array($criteria[PROJECT_ID], array_column($projects, "id"));

if ($criteria[PROJECT_ID] && $isProjectExist === false) {
    http_response_code(404);
    exit();
}

$projectsSideTemplate = includeTemplate("projects-side.php", [
    "projects" => $projects,
    "scriptName" => pathinfo(__FILE__, PATHINFO_BASENAME),
    "projectId" => $criteria[PROJECT_ID]
]);

$pageContent = includeTemplate("main.php", [
    "projectsSideTemplate" => $projectsSideTemplate,
    "tasks" => $tasks,
    "showCompleteTasks" => $showCompleteTasks,
    "scriptName" => pathinfo(__FILE__, PATHINFO_BASENAME),
    "btnActive" => $criteria["expire"],
    "errors" => $errors
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
]);

print($layoutContent);

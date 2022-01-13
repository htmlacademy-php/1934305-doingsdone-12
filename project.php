<?php
/* @var mysqli $con
 * @var string $title
 * @var int $userId
*/

require_once "init.php";

if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

$projectId = filter_input(INPUT_GET, "project_id", FILTER_SANITIZE_NUMBER_INT);
$projects = getProjects($con, $userId);

$errors = [];
$projectsSideTemplate = includeTemplate("projects-side.php", [
    "projects" => $projects,
    "scriptName" => pathinfo("index.php", PATHINFO_BASENAME),
    "projectId" => $projectId
]);

$pageContent = includeTemplate("form-project.php", [
    "projectsSideTemplate" => $projectsSideTemplate,
    "errors" => $errors
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
]);

print($layoutContent);

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
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $projectForm = makeProjectFormArray();
    $projectForm["user_id"] = $userId;

    $errors = validateProjectForm($projectForm, $con);

    if (empty($errors)) {
        $res = createNewProject($con, $projectForm);

        if ($res) {
            header("Location: index.php");
        } else {
            $mysqliError = mysqli_error($con);
            renderError($mysqliError);
        }
        exit();
    }
}

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

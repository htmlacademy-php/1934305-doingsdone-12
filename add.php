<?php
/* @var mysqli $con
*/

require_once "init.php";

$title = "Дела в порядке";

$userId = 1;
$projectId = filter_input(INPUT_GET, "project_id", FILTER_SANITIZE_NUMBER_INT);
$projects = getProjects($con, $userId);
$projectsId = array_column($projects, "id");

$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $taskForm = filter_input_array(INPUT_POST);
    $taskForm["user_id"] = $userId;
    $taskForm["file"] = "";

    $errors = validateTaskForm($taskForm, $projectsId);
    $errors = array_filter($errors);

    if (!empty($_FILES["file"]["name"]) && empty($errors)) {
        $pathFile = validateFileUpload();

        if ($pathFile === null) {
            $errors["file"] = "Ошибка загрузки файла";
        } else {
            $taskForm["file"] = $pathFile;
        }
    }

    if (empty($errors)) {
        $res = createNewTask($con, $taskForm);

        if ($res) {
            header("Location: index.php");
        } else {
            $mysqliError = mysqli_error($con);
            renderError($mysqliError);
            unlink($taskForm["file"]);
        }
        exit();
    }
}

$projectsSideTemplate = includeTemplate("projects-side.php", [
    "projects" => $projects,
    "scriptName" => pathinfo("index.php", PATHINFO_BASENAME),
    "projectId" => $projectId
]);

$pageContent = includeTemplate("form-task.php", [
    "projectsSideTemplate" => $projectsSideTemplate,
    "projects" => $projects,
    "errors" => $errors
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
    "addScript" => pathinfo("add.php", PATHINFO_BASENAME)
]);

print($layoutContent);

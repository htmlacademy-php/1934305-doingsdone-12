<?php
/* @var mysqli $con
*/

require_once("init.php");

$title = "Дела в порядке";

$userId = 1;
$projectId = filter_input(INPUT_GET, "project_id", FILTER_SANITIZE_NUMBER_INT);
$projects = getProjects($con, $userId);
$projectsId = array_column($projects, "id");

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required = ["project_id", "name", "date"];

    $taskForm = filter_input_array(INPUT_POST);

    foreach ($taskForm as $key => $value) {
        switch ($key) {
            case "project_id":
                $errors[$key] = validateProject($value, $projectsId);
                break;
            case "name":
                $errors[$key] = validateTaskName($value);
                break;
            case "date":
                $errors[$key] = validateDate($value);
                break;
            default:
        }
    }
    // TODO: обработать тип файла. Возвращать ощибку формы, если файл не загрузился
    // TODO: после загрузки файла сгенерировать хеш для сохранения уникальности файла
    $errors = array_filter($errors);


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

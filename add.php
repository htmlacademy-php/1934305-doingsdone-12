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

    $rules = [
        "project_id" => function ($value) use ($projectsId) {
            return validateProject($value, $projectsId);
        },
        "name" => function ($value) {
            return validateTaskName($value);
        },
        "date" => function ($value) {
            return validateDate($value);
        }
    ];

    // TODO: переименовать в $taskForm
    $task = filter_input_array(INPUT_POST);
    // TODO: избавиться от коллбеков и вызывать функции валидаций явно
    foreach ($task as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }

        //TODO: проверять на заполненность в отдельных функциях
        if (in_array($key, $required) && empty(trim($value))) {
            $errors[$key] = "Поле " . mapKeyToFieldName($key) . " надо заполнить";
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

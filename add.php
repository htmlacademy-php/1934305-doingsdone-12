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
    $taskForm = filter_input_array(INPUT_POST);
    $taskForm["user_id"] = $userId;
    $taskForm["file"] = "";

    foreach ($taskForm as $key => $value) {
        switch ($key) {
            case "project_id":
                // приравниваю значение $value к инту, чтобы если кто-то попытается отправить форму
                // с пустым значением или строку она $value просто преобразовался бы в 0
                $errors[$key] = validateProject((int) $value, $projectsId);
                break;
            case "name":
                $errors[$key] = validateTaskName($value);
                break;
            case "end_time":
                $errors[$key] = validateDate($value, date_create()->format("Y-m-d"));
                break;
            default:
        }
    }

    if (!empty($_FILES["file"]["name"]) && empty($errors)) {
        $path = $_FILES["file"]["tmp_name"];
        $filename = uniqid() . "__" . $_FILES["file"]["name"];

        $isMoved = move_uploaded_file($path, "uploads/" . $filename);

        if ($isMoved == false) {
            $errors["file"] = "Ошибка загрузки файла";
        }

        $taskForm["file"] = "uploads/" . $filename;
    }

    $errors = array_filter($errors);

    if (empty($errors)) {
        $res = createNewTask($con, $taskForm);

        if ($res) {
            header("Location: index.php");
        } else {
            $mysqliError = mysqli_error($con);
            renderError($mysqliError);
            exit();
        }
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

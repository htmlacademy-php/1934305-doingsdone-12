<?php
/* @var mysqli $con
*/

require_once "init.php";

$title = "Дела в порядке";

$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $registerForm = filter_input_array(INPUT_POST);
    $errors = validateRegisterForm($registerForm, $con);
    $errors = array_filter($errors);

    if (empty($errors)) {
        $res = createNewUser($con, $registerForm);

        if ($res) {
            header("Location: index.php");
        } else {
            $mysqliError = mysqli_error($con);
            renderError($mysqliError);
        }
        exit();
    }
}

$pageContent = includeTemplate("register-form.php", ["errors" => $errors]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
    "addScript" => pathinfo("add.php", PATHINFO_BASENAME)
]);

print($layoutContent);

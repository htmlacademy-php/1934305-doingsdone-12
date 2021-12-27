<?php
/* @var mysqli $con
 * @var string $title
*/

require_once "init.php";

$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $registerForm = makeRegisterFormArray();
    $errors = validateRegisterForm($registerForm, $con);

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

if (isset($_SESSION["user"])) {
    header("Location: /index.php");
    exit();
}

$pageContent = includeTemplate("register-form.php", [
    "errors" => $errors,
    "authScript" => pathinfo("auth.php", PATHINFO_BASENAME)
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
    "addScript" => pathinfo("add.php", PATHINFO_BASENAME),
    "authScript" => pathinfo("auth.php", PATHINFO_BASENAME)
]);

print($layoutContent);

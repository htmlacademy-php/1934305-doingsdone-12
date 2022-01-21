<?php

/* @var string $title
 * @var mysqli $con
*/
require_once "init.php";

$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $authForm = makeAuthFormArray();
    $errors = validateAuthForm($authForm, $con);

    if (empty($errors)) {
        $user = getUserCredentials($con, $authForm["email"]);
        if ($user !== null) {
            $errors["password"] = createUserSession($authForm["password"], $user);
        } else {
            $errors["email"] = "Такой пользователь не найден";
        }
    }

    if (empty($errors)) {
        header("Location: /index.php");
        exit();
    }
}

if (isset($_SESSION["user"])) {
    header("Location: /index.php");
    exit();
}


$pageContent = includeTemplate("form-authorization.php", [
    "errors" => $errors
]);

$layoutContent = includeTemplate("layout.php", [
    "content" => $pageContent,
    "title" => $title,
]);

print($layoutContent);

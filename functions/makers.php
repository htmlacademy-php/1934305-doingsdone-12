<?php

/**
 * Формирует и заполняет массив из данных полученных из форм
 * @param array $expectedFields ожидаемые поля для нового массива
 * @return array отфильтрованный массив данных из формы
 */
function makeArrayFromFormInput(array $expectedFields): array
{
    $filteredPost = filter_input_array(INPUT_POST);

    $res = [];
    foreach ($expectedFields as $field) {
        $res[$field] = $filteredPost[$field] ?? null;
    }

    return $res;
}




/**
 * Создаёт массив задачи из введённых данных из формы задач
 * @return array отфильтрованный массив данных из формы задач
 */
function makeTaskFormArray(): array
{
    $expectedFields = ["name", "project_id", "end_time", "user_id", "file"];

    return makeArrayFromFormInput($expectedFields);
}

/**
 * Создаёт массив задачи из введённых данных из формы регистрации
 * @return array отфильтрованный массив данных из формы регистрации
 */
function makeRegisterFormArray(): array
{
    $expectedFields = ["email", "password", "name"];

    return makeArrayFromFormInput($expectedFields);
}

/**
 * Создаёт массив из учётных данных пользователя
 * @return array отфильтрованный массив данных из формы аутентификации
 */
function makeAuthFormArray(): array
{
    $expectedField = ["email", "password"];

    return makeArrayFromFormInput($expectedField);
}

/**
 * Валидирует юзера по данным входа и создаёт сессию.
 * @param string $formPassword - пароль из формы
 * @param array $user - ассоциативный массив пользователя из БД
 * @return string|null - строку с описание ошибки
 * или null если пароль верифицирован
 */
function createUserSession(string $formPassword, array $user): ?string
{
    if (password_verify($formPassword, $user["password"])) {
        $_SESSION["user"] = $user;

        return null;
    }

    return "Неверный пароль";
}

/**
 * Создаёт массив из формы для добавления проекта
 * @return array отфильтрованный массив данных
 * из формы для добавления проекта
 */
function makeProjectFormArray(): array
{
    $expectedField = ["project_name"];

    return makeArrayFromFormInput($expectedField);
}

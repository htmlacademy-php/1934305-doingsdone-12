<?php

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * isDateValid('2019-01-01'); // true
 * isDateValid('2016-02-29'); // true
 * isDateValid('2019-04-31'); // false
 * isDateValid('10.10.2010'); // false
 * isDateValid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function isDateValid(string $date): bool
{
    $formatToCheck = 'Y-m-d';
    $dateTimeObj = date_create_from_format($formatToCheck, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Определяет является ли дата датой, для выполнения которой осталось меньше 24 часов
 * @param string|null $dateStr дата в виде строки так же может быть null
 * @param DateTime $dtNow текущая дата
 * @return bool если количество часов до выполнения задачи меньше или равно 24 возвращает true, иначе false
 */
function isTaskImportant(?string $dateStr, DateTime $dtNow): bool
{
    if ($dateStr === null) {
        return false;
    }

    $dtEnd = date_create($dateStr);

    // Т.к. в данных у даты не указанны часы, то дата создаётся в часовом диапазоне 00:00
    // но в задаче подразумевается, что дата считается с конца дня, а не с начала, для этого
    // добавляю еще 24 часа к созданной дате.
    $dtEnd->modify("+1 day");

    $diff = $dtNow->diff($dtEnd);

    $hours = (int)$diff->format("%r%h");
    $hours += (int)$diff->format("%r%a") * 24;

    if ($hours <= 24) {
        return true;
    }

    return false;
}

/**
 * Проверяет строку на пустоту. Возвращает сообщение об ошибке или null
 * @param string $value строка из формы
 * @return string|null сообщение об ошибке или null
 */
function validateTaskName(string $value): ?string
{
    $valueLen = mb_strlen(trim($value));

    if ($valueLen == 0) {
        return "Поле название надо заполнить";
    }

    if ($valueLen > 255) {
        return "Название не должно превышать размер в 255 символов";
    }

    return null;
}

/**
 * Проверяет является ли выбранное имя проекта существующим для этого пользователя.
 * Возвращает сообщение об ошибке или null
 * @param int $id номер проекта из формы
 * @param array $projectsId массив id проектов
 * @return string|null сообщение об ошибке или null
 */
function validateProject(int $id, array $projectsId): ?string
{
    if (!in_array($id, $projectsId)) {
        return "Указан несуществующий проект";
    }

    return null;
}

/**
 * Проверяет правильность формата введённой даты
 * @param string $dateStr дата в строковом представлении
 * @param string $curDate текущая дата в строковом представлении
 * @return string|null сообщение об ошибке или null
 */
function validateDate(string $dateStr, string $curDate): ?string
{
    if (empty(trim($dateStr))) {
        return null;
    }

    if (isDateValid($dateStr) == false) {
        return "Неверный формат даты";
    }

    if ($curDate > $dateStr) {
        return "Выбранная дата должна быть больше или равна текущей";
    }

    return null;
}

/**
 * Проверяет данные введённые из формы на ошибки
 * @param array $taskForm массив данных введённых из формы
 * @param array $projectsId массив id проектов для валидации
 * @param string $curDate текущая дата
 * @return array массив ошибок
 */
function validateTaskForm(array $taskForm, array $projectsId, string $curDate): array
{
    $errors = [];
    // приравниваю значение из $taskForm к инту, чтобы если кто-то попытается отправить форму
    // с пустым значением или строку это значение просто преобразовалось бы к 0
    $errors["project_id"] = validateProject((int)$taskForm["project_id"], $projectsId);
    $errors["name"] = validateTaskName($taskForm["name"]);
    $errors["end_time"] = validateDate($taskForm["end_time"], $curDate);

    return array_filter($errors);
}

/**
 * Генерирурет уникальное имя загруженному файлу и переносит его из временной папки в папку проекта
 * @return string|null путь загруженного файла или null
 */
function validateFileUpload(): ?string
{
        $path = $_FILES["file"]["tmp_name"];
        $filename = uniqid() . "__" . $_FILES["file"]["name"];

        $isMoved = move_uploaded_file($path, "uploads/" . $filename);

        if ($isMoved === false) {
            return null;
        }

        return "uploads/" . $filename;
}

/**
 * Проверяет на корректность введёный email адрес из формы специфичной только для регистрации
 * @param string $email -- введённый email адрес пользователем
 * @param bool $isEmailInDB -- результат проверки на занятность email адреса
 * @return string|null сообщение об ошибке или null
 */
function validateEmailReg(string $email, bool $isEmailInDB): ?string
{
    $email = trim($email);

    if (mb_strlen($email) > 255) {
        return "E-mail адрес слишком длинный";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "E-mail введён некорректно";
    }

    if ($isEmailInDB === true) {
        return "Данный E-mail адрес уже занят";
    }

    return null;
}

/**
 * Проверяет на корректность введёный email адрес из формы специфичной только для аутентификации
 * @param string $email -- введённый email адрес пользователем
 * @param bool $isEmailInDB -- результат проверки на занятность email адреса
 * @return string|null сообщение об ошибке или null
 */
function validateEmailAuth(string $email, bool $isEmailInDB): ?string
{
    $email = trim($email);

    if (mb_strlen($email) > 255) {
        return "E-mail адрес слишком длинный";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "E-mail введён некорректно";
    }

    if ($isEmailInDB === false) {
        return "Пользователя с данным E-mail адресом не существует";
    }

    return null;
}

/**
 * Проверяет на корректность введённое имя пользователя из формы
 * @param string $userName -- введённое имя пользователем
 * @return string|null сообщение об ошибке или null
 */
function validateUserName(string $userName): ?string
{
    $userName = trim($userName);

    if (mb_strlen($userName) === 0) {
        return "Введите имя пользователя";
    }

    if (mb_strlen($userName) > 70) {
        return "Слишком длинное имя";
    }

    return null;
}

/**
 * Проверяет на корректность введённый пароль из формы
 * @param string $password -- введённый пароль
 * @return string|null сообщение об ошибке или null
 */
function validatePassword(string $password): ?string
{
    if (strpos($password, " ") !== false) {
        return "Пароль не должен содержать пробельные символы";
    }

    if (mb_strlen($password) < 8) {
        return "Пароль должен содержать минимум 8 символов";
    }

    if (mb_strlen($password) > 60) {
        return "Пароль не должен превышать лимит 60-ти символов";
    }

    return null;
}

/**
 * Проверяет данные введённые из формы на ошибки
 * @param array $registerForm массив данных введённых из формы
 * @param mysqli $con - объект подключения к БД
 * @return array массив ошибок
 */
function validateRegisterForm(array $registerForm, mysqli $con): array
{
    $isEmailInDB = isEmailExistsInDB($con, $registerForm["email"]);
    $errors = [];
    $errors["email"] = validateEmailReg($registerForm["email"], $isEmailInDB);
    $errors["password"] = validatePassword($registerForm["password"]);
    $errors["name"] = validateUserName($registerForm["name"]);

    return array_filter($errors);
}


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
 * Проверяет данные входа введённые из формы на ошибки
 * @param array $authForm массив данных введённых из формы
 * @param mysqli $con - объект подключения к БД
 * @return array массив ошибок
 */
function validateAuthForm(array $authForm, mysqli $con): array
{
    $isEmailInDb = isEmailExistsInDB($con, $authForm["email"]);
    $errors = [];
    $errors["email"] = validateEmailAuth($authForm["email"], $isEmailInDb);
    $errors["password"] = validatePassword($authForm["password"]);

    return array_filter($errors);
}

/**
 * Валидирует юзера по данным входа и создаёт сессию.
 * @param string $formPassword - пароль из формы
 * @param array $user - ассоциативный массив пользователя из БД
 * @return string - строку с описание ошибки или null если пароль верифицирован
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
 * @return array отфильтрованный массив данных из формы для добавления проекта
 */
function makeProjectFormArray(): array
{
    $expectedField = ["name"];

    return makeArrayFromFormInput($expectedField);
}

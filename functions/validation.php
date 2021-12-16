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
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function getNounPluralForm(int $number, string $one, string $two, string $many): string
{
    $number = (int)$number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
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
    } elseif ($valueLen > 255) {
        return "Название не должно превышать размер в 255 символов";
    } else {
        return null;
    }
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
    } elseif (isDateValid($dateStr) == false) {
        return "Неверный формат даты";
    } elseif ($curDate > $dateStr) {
        return "Выбранная дата должна быть больше или равна текущей";
    } else {
        return null;
    }
}

/**
 * Проверяет данные введённые из формы на ошибки
 * @param array $taskForm массив данных введённых из формы
 * @param array $projectsId массив id проектов для валидации
 * @return array массив ошибок
 */
function validateTaskForm(array $taskForm, array $projectsId): array
{
    $errors = [];
    // приравниваю значение из $taskForm к инту, чтобы если кто-то попытается отправить форму
    // с пустым значением или строку это значение просто преобразовалось бы к 0
    $errors["project_id"] = validateProject((int)$taskForm["project_id"], $projectsId);
    $errors["name"] = validateTaskName($taskForm["name"]);
    $errors["end_time"] = validateDate($taskForm["end_time"], date_create()->format("Y-m-d"));

    return $errors;
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
 * Проверяет на корректность введёный email адрес из формы
 * @param string $email -- введённый email адрес пользователем
 * @param array $emails -- массив всех уже записанных email адресов из БД
 * @return string|null сообщение об ошибке или null
 */
function validateEmail(string $email, array $emails): ?string
{
    $email = trim($email);

    if (mb_strlen($email) > 255) {
        return "E-mail адрес слишком длинный";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "E-mail введён некорректно";
    } elseif (in_array($email, $emails)) {
        return "Данный E-mail адрес уже занят";
    } else {
        return null;
    }
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
    } elseif (mb_strlen($userName) > 70) {
        return "Слишком длинное имя";
    } else {
        return null;
    }
}

/**
 * Проверяет на корректность введённый пароль из формы
 * @param string $password -- введённый пароль
 * @return string|null сообщение об ошибке или null
 */
function validatePassword(string $password): ?string
{
    $password = trim($password);

    if (strpos($password, " ") !== false) {
        return "Пароль не должен содержать пробельные символы";
    } elseif (mb_strlen($password) < 8) {
        return "Пароль должен содержать минимум 8 символов";
    } elseif (mb_strlen($password) > 60) {
        return "Пароль не должен превышать лимит 60-ти символов";
    } else {
        return null;
    }
}

/**
 * Проверяет данные введённые из формы на ошибки
 * @param array $registerForm массив данных введённых из формы
 * @param array $emails -- почтовые адресы из полученные из БД
 * @return array массив ошибок
 */
function validateRegisterForm(array $registerForm, array $emails): array
{
    $errors = [];
    $errors["email"] = validateEmail($registerForm["email"], $emails);
    $errors["password"] = validatePassword($registerForm["password"]);
    $errors["name"] = validateUserName($registerForm["name"]);

    return $errors;
}

<?php

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function dbGetPrepareStmt(mysqli $link, string $sql, array $data): mysqli_stmt
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmtData = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } elseif (is_string($value)) {
                $type = 's';
            } elseif (is_double($value)) {
                $type = 'd';
            }

            $types .= $type;
            $stmtData[] = $value;
        }

        $values = array_merge([$stmt, $types], $stmtData);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает результат работы подготовленного выражения для дальнейшей обраотки данных пользователя
 * @param string $sqlQuery - подготовленная строка SQL запроса
 * @param array $params - параметры запроса
 * @param @con - информация для соединения с БД
 * @return mysqli_result - результат подготовленного выражения
 */
function getUserStmtResult(string $sqlQuery, array $params, $con): mysqli_result
{
    $preparedStatement = dbGetPrepareStmt($con, $sqlQuery, $params);
    mysqli_stmt_execute($preparedStatement);

    return mysqli_stmt_get_result($preparedStatement);
}

/**
 * Возвращает результат работы подготовленного выражения для дальнейшей обраотки данных пользователя
 * @param array $db - ассоциативный массив с конфигом для подключения к базе данных
 * @return mysqli - объект подключения к БД
 */
function makeConnection(array $db): mysqli
{
    $con = mysqli_connect($db["host"], $db["user"], $db["password"], $db["database"]);

    if ($con === false) {
        $error = mysqli_connect_error();
        renderError($error);
        exit();
    }

    mysqli_set_charset($con, "utf8");

    return $con;
}

/**
 * Возвращает массив всех проектов из БД
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - номер айди пользователя
 * @return array - массив проектов
 */
function getProjects(mysqli $con, int $userId): array
{
    $selectProjectsById =
        "SELECT p.id, p.name, COUNT(t.name) AS amount
            FROM projects AS p
            LEFT JOIN tasks AS t ON t.project_id = p.id
            WHERE
                p.user_id = ?
            GROUP BY
                p.id, p.name;";

    $result = getUserStmtResult($selectProjectsById, ["user_id" => $userId], $con);
    if ($result) {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return $projects;
}

/**
 * Возвращает массив задач из БД
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - номер айди пользователя
 * @return array - массив задач
 */
function getTasksAll(mysqli $con, int $userId): array
{
    $selectTasksById =
    "SELECT t.name AS task_name, t.end_time AS date, p.name AS project, t.status AS is_finished
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ?";


    $result = getUserStmtResult($selectTasksById, ["user_id" => $userId], $con);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return $tasks;
}

/**
 * Возвращает массив задач из БД соотвествующего $projectId
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - номер айди пользователя
 * @param int $projectId - номер айди проекта
 * @return array - массив задач
 */
function getTasksByProjectId(mysqli $con, int $userId, int $projectId): array
{
    $selectTasksById =
    "SELECT t.name AS task_name, t.end_time AS date, p.name AS project, t.status AS is_finished
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND p.id = ?";


    $result = getUserStmtResult($selectTasksById, ["user_id" => $userId, "project_id" => $projectId], $con);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return $tasks;
}

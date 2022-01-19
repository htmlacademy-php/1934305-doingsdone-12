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
    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
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
        "SELECT t.id AS task_id, t.name AS task_name, t.end_time AS date, p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ?";


    $result = getUserStmtResult($selectTasksById, ["user_id" => $userId], $con);
    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
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
        "SELECT t.id AS task_id, t.name AS task_name, t.end_time AS date, p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND p.id = ?";


    $result = getUserStmtResult($selectTasksById, ["user_id" => $userId, "project_id" => $projectId], $con);
    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Создаёт новую задачу в БД
 * @param mysqli $con - объект подключения к БД
 * @param array $taskForm - задача принятая из формы
 * @return bool - результат выполнения запроса к БД
 */
function createNewTask(mysqli $con, array $taskForm): bool
{
    if ($taskForm["end_time"] === "") {
        $taskForm["end_time"] = null;
    }

    $sqlQuery = "INSERT INTO tasks (name, project_id, end_time, user_id, file, creation_time)
                    VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = dbGetPrepareStmt($con, $sqlQuery, $taskForm);

    return mysqli_stmt_execute($stmt);
}

/**
 * Проверяет существует ли почтовая запись в БД
 * @param mysqli $con - объект подключения к БД
 * @param string $email - почтовый адрес введённый из формы
 * @return bool -- возвращает true, если существует. Возвращает false в ином случае
 */
function isEmailExistsInDB(mysqli $con, string $email): bool
{
    $sqlQuery = "SELECT id FROM users WHERE email = ?";

    $stmt = dbGetPrepareStmt($con, $sqlQuery, ["email" => $email]);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return mysqli_num_rows($result);
}

/**
 * Создаёт нового пользователя в БД
 * @param mysqli $con - объект подключения к БД
 * @param array $registerForm - данные пользователя для регистрации из формы
 * @return bool - результат выполнения запроса к БД
 */
function createNewUser(mysqli $con, array $registerForm): bool
{
    $password = password_hash($registerForm["password"], PASSWORD_DEFAULT);

    $sqlQuery = "INSERT INTO users (registration_time, email, password, name)
                    VALUES (NOW(), ?, ?, ?)";

    $stmt = dbGetPrepareStmt($con, $sqlQuery, [$registerForm["email"], $password, $registerForm["name"]]);

    return mysqli_stmt_execute($stmt);
}

/**
 * Получает данные для входа пользователя из БД
 * @param mysqli $con - объект подключения к БД
 * @param string $email - почтовый адрес введённый из формы
 * @return array - возвращает ассоциативный массив с данными о пользователе или null
 */
function getUserCredentials(mysqli $con, string $email): ?array
{
    $sqlQuery = "SELECT * FROM users WHERE email = ?";

    $stmt = dbGetPrepareStmt($con, $sqlQuery, ["email" => $email]);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    $user = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if (empty($user)) {
        return null;
    }

    return $user[0];
}

/**
 * Возвращает массив задач из БД по ключевым словам или null
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - номер айди пользователя
 * @return array|null  - массив задач или null
 */
function getTasksByQuery(mysqli $con, int $userId, string $query): ?array
{
    $selectTasksByQuery =
        "SELECT t.id AS task_id, t.name AS task_name, t.end_time AS date, p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND MATCH(t.name) AGAINST(? IN BOOLEAN MODE)";


    $result = getUserStmtResult($selectTasksByQuery, ["user_id" => $userId, "query" => trim($query)], $con);
    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return empty($tasks) ? null : $tasks;
}

/**
 * Проверяет существует ли проект для конкретного пользователя
 * @param mysqli $con - объект подключения к БД
 * @param string $project - имя проекта введённый из формы
 * @param int $userId - идентификатор пользователя
 * @return bool -- возвращает true, если существует. Возвращает false в ином случае
 */
function isProjectExistsInDB(mysqli $con, string $project, int $userId): bool
{
    $sqlQuery = "SELECT id FROM projects WHERE user_id = ? AND name = ?";

    $stmt = dbGetPrepareStmt($con, $sqlQuery, ["user_id" => $userId, "name" => $project]);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return mysqli_num_rows($result);
}

/**
 * Создаёт новый проект для пользователя
 * @param mysqli $con - объект подключения к БД
 * @param array $projectForm - имя нового проекта пользователя из формы
 * @return bool - результат выполнения запроса к БД
 */
function createNewProject(mysqli $con, array $projectForm): bool
{
    $sqlQuery = "INSERT INTO projects (name, user_id) VALUES (?, ?)";

    $stmt = dbGetPrepareStmt($con, $sqlQuery, [$projectForm["project_name"], $projectForm["user_id"]]);

    return mysqli_stmt_execute($stmt);
}

/**
 * Переключает статус готовности задачи проекта
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - идентификатор пользователя
 * @param int $taskId - идентификатор задачи
 * @return bool - результат выполнения запроса к БД
 */
function updateStatusTask(mysqli $con, int $userId, int $taskId): bool
{
    $sqlQuery = "UPDATE tasks SET status = IF(status=1, 0, 1) WHERE user_id = ? AND id = ?";

    $stmt = dbGetPrepareStmt($con, $sqlQuery, ["user_id" => $userId, "id" => $taskId]);

    mysqli_stmt_execute($stmt);

    return mysqli_stmt_affected_rows($stmt);
}


/**
 * Возвращает массив задач на сегодня из БД
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - номер айди пользователя
 * @param string $date - дата задач
 * @return array - массив задач
 */
function getTasksByDate(mysqli $con, int $userId, string $date): array
{
    $selectTasksById =
        "SELECT t.id AS task_id, t.name AS task_name, t.end_time AS date, p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND t.end_time = ?";


    $result = getUserStmtResult($selectTasksById, ["user_id" => $userId, "end_time" => $date], $con);
    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Возвращает массив просроченных задач из БД
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - номер айди пользователя
 * @param string $date - дата задач
 * @return array - массив задач
 */
function getOverdueTasks(mysqli $con, int $userId, string $date): array
{
    $selectTasksById =
        "SELECT t.id AS task_id, t.name AS task_name, t.end_time AS date, p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND t.end_time < ? AND t.status = 0";


    $result = getUserStmtResult($selectTasksById, ["user_id" => $userId, "end_time" => $date], $con);
    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

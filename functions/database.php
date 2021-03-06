<?php

/**
 * Создает подготовленное выражение на
 * основе готового SQL запроса и переданных данных
 *
 * @param mysqli $link Ресурс соединения
 * @param string $sql SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function dbGetPrepareStmt(mysqli $link, string $sql, array $data): mysqli_stmt
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать'
            . 'подготовленное выражение: ' . mysqli_error($link);
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
            $errorMsg = 'Не удалось связать подготовленное'
                . 'выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает результат работы подготовленного
 * выражения для дальнейшей обраотки данных пользователя
 * @param string $sqlQuery - подготовленная строка SQL запроса
 * @param array $params - параметры запроса
 * @param @con - информация для соединения с БД
 * @return mysqli_result|null - результат подготовленного выражения
 */
function getUserStmtResult(string $sqlQuery, array $params, $con): ?mysqli_result
{
    $preparedStatement = dbGetPrepareStmt($con, $sqlQuery, $params);
    mysqli_stmt_execute($preparedStatement);

    $result = mysqli_stmt_get_result($preparedStatement);

    return (!$result) ? null : $result;
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
 * @param bool $showCompleteTasks - значение для отображения
 * законченных задач из БД
 * @return array - массив задач
 */
function getTasksAll(mysqli $con, int $userId, bool $showCompleteTasks): array
{
    $allTasks =
        "SELECT t.id AS task_id, t.name AS task_name, DATE_FORMAT(t.end_time, '%d.%m.%Y')
        AS date, p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ?";

    $incompleteTasks =
        "SELECT t.id AS task_id, t.name AS task_name, DATE_FORMAT(t.end_time, '%d.%m.%Y')
        AS date, p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND t.status = 0";

    $query = ($showCompleteTasks) ? $allTasks : $incompleteTasks;
    $result = getUserStmtResult($query, ["user_id" => $userId], $con);
    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Возвращает массив задач из БД соответствующего $projectId
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - номер айди пользователя
 * @param int $projectId - номер айди проекта
 * @param bool $showCompleteTasks - значение для отображения
 * законченных задач из БД
 * @return array - массив задач
 */
function getTasksByProjectId(mysqli $con, int $userId, int $projectId, bool $showCompleteTasks): array
{
    $allTasksById =
        "SELECT t.id AS task_id, t.name AS task_name,
       DATE_FORMAT(t.end_time, '%d.%m.%Y') AS date, p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND p.id = ?";

    $incompleteTasksById =
        "SELECT t.id AS task_id, t.name AS task_name,
        DATE_FORMAT(t.end_time, '%d.%m.%Y') AS date, p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND p.id = ? AND t.status = 0";

    $query = ($showCompleteTasks) ? $allTasksById : $incompleteTasksById;

    $result = getUserStmtResult($query, ["user_id" => $userId, "project_id" => $projectId], $con);
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
 * @return bool -- возвращает true, если существует.
 * Возвращает false в ином случае
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
 * @return array|null - возвращает ассоциативный
 * массив с данными о пользователе или null
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
 * @param bool $showCompleteTasks - значение для отображения
 * законченных задач из БД
 * @return array|null  - массив задач или null
 */
function getTasksByQuery(mysqli $con, int $userId, string $query, bool $showCompleteTasks): ?array
{
    $allTasks =
        "SELECT t.id AS task_id, t.name AS task_name, DATE_FORMAT(t.end_time, '%d.%m.%Y') AS date,
       p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND MATCH(t.name) AGAINST(? IN BOOLEAN MODE)";

    $incompleteTasks =
               "SELECT t.id AS task_id, t.name AS task_name, DATE_FORMAT(t.end_time, '%d.%m.%Y') AS date,
       p.name AS project, t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND t.status = 0 AND MATCH(t.name) AGAINST(? IN BOOLEAN MODE)";

    $querySql = ($showCompleteTasks) ? $allTasks : $incompleteTasks;

    $result = getUserStmtResult($querySql, ["user_id" => $userId, "query" => trim($query)], $con);
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
 * @return bool -- возвращает true,
 * если существует. Возвращает false в ином случае
 */
function isProjectExistsInDB(mysqli $con, string $project, int $userId): bool
{
    $query = "SELECT id FROM projects WHERE user_id = ? AND name = ?";

    $stmt = dbGetPrepareStmt($con, $query, ["user_id" => $userId, "name" => $project]);

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
    $query = "INSERT INTO projects (name, user_id) VALUES (?, ?)";

    $stmt = dbGetPrepareStmt($con, $query, [$projectForm["project_name"], $projectForm["user_id"]]);

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
 * @param bool $showCompleteTasks - значение для отображения
 * законченных задач из БД
 * @return array - массив задач
 */
function getTasksByDate(mysqli $con, int $userId, string $date, bool $showCompleteTasks): array
{
    $allTasks =
        "SELECT t.id AS task_id, t.name AS task_name, DATE_FORMAT(t.end_time, '%d.%m.%Y') AS date, p.name AS project,
       t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND t.end_time = ?";

    $incompleteTasks =
        "SELECT t.id AS task_id, t.name AS task_name, DATE_FORMAT(t.end_time, '%d.%m.%Y') AS date, p.name AS project,
       t.status AS is_finished, t.file
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ? AND t.end_time = ? AND t.status = 0";

    $query = ($showCompleteTasks) ? $allTasks : $incompleteTasks;
    $result = getUserStmtResult($query, ["user_id" => $userId, "end_time" => $date], $con);
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
        "SELECT t.id AS task_id, t.name AS task_name, DATE_FORMAT(t.end_time, '%d.%m.%Y') AS date, p.name AS project,
       t.status AS is_finished, t.file
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


/**
 * Возвращает массив невыполненных задач,
 * имена пользователей на сегодня из БД
 * @param mysqli $con - объект подключения к БД
 * @param string $date - дата задач
 * законченных задач из БД
 * @return array - массив задач
 */
function getUsersTasksByDate(mysqli $con, string $date): array
{
    $query =
        "SELECT u.id, u.name AS user_name, u.email, GROUP_CONCAT(t.name SEPARATOR ', ') AS tasks_names,
            DATE_FORMAT(t.end_time, '%d.%m.%Y') as date
            FROM users AS u JOIN tasks as t ON t.user_id = u.id
                    WHERE t.status = 0 AND t.end_time = ?
            GROUP BY u.id, u.name, u.email, t.end_time";
    $result = getUserStmtResult($query, ["end_time" => $date], $con);
    if (!$result) {
        $error = mysqli_error($con);
        renderError($error);
        exit();
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

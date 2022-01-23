<?php

/**
 * Обобщает в одной функции получение всех GET-запросов
 * @return array ассоциативный массив из GET параметров
 */
function getQueriesWrapper(): array
{
    $queries = [];

    $queries[PROJECT_ID] = (int)filter_input(INPUT_GET, "project_id", FILTER_SANITIZE_NUMBER_INT);

    $queries[TASK_ID] = (int)filter_input(INPUT_GET, "task_id", FILTER_SANITIZE_NUMBER_INT);

    $queries[CURRENT_DAY] = (int)filter_input(INPUT_GET, "current_day", FILTER_SANITIZE_NUMBER_INT);
    $queries[TOMORROW] = (int)filter_input(INPUT_GET, "tomorrow", FILTER_SANITIZE_NUMBER_INT);
    $queries[OVERDUE] = (int)filter_input(INPUT_GET, "overdue", FILTER_SANITIZE_NUMBER_INT);
    $queries[QUERY] = filter_input(INPUT_GET, "query", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    return $queries;
}

/**
 * Обёртка над всеми запросами задач в базу
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - идентификатор пользователя
 * @param array $queryStringsValues - ассоциативный массив
 * со значениями из GET - запросов
 * @param bool $showCompleteTasks - значение для отображения
 * законченных задач из БД
 * @return array|null - массив задач
 */
function getTasksWrapper(mysqli $con, int $userId, array &$queryStringsValues, bool $showCompleteTasks): ?array
{
    if ($queryStringsValues[PROJECT_ID]) {
        return getTasksByProjectId($con, $userId, $queryStringsValues[PROJECT_ID], $showCompleteTasks);
    }

    if (isset($_GET[QUERY])) {
        $query = filter_input(INPUT_GET, QUERY, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return getTasksByQuery($con, $userId, $query, $showCompleteTasks);
    }

    if ($queryStringsValues[CURRENT_DAY] === 1) {
        return getTasksByDate($con, $userId, date_create()->format("Y-m-d"), $showCompleteTasks);
    }

    if ($queryStringsValues[TOMORROW] === 1) {
        return getTasksByDate($con, $userId, date_create()->modify("+1 day")->format("Y-m-d"), $showCompleteTasks);
    }

    if ($queryStringsValues[OVERDUE] === 1) {
        return getOverdueTasks($con, $userId, date_create()->format("Y-m-d"));
    }

    $queryStringsValues[ALL_TASKS] = 1;

    return getTasksAll($con, $userId, $showCompleteTasks);
}

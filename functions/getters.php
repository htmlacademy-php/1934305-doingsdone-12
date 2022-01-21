<?php

/**
 * Обобщает в одной функции получение всех GET-запросов
 * @return array ассоциативный массив из GET параметров
 */
function getQueriesWrapper(): array
{
    $queries = [];

    $queries["project_id"] = (int)filter_input(INPUT_GET, "project_id", FILTER_SANITIZE_NUMBER_INT);

    $queries["task_id"] = (int)filter_input(INPUT_GET, "task_id", FILTER_SANITIZE_NUMBER_INT);

    $queries["current_day"] = (int)filter_input(INPUT_GET, "current_day", FILTER_SANITIZE_NUMBER_INT);
    $queries["tomorrow"] = (int)filter_input(INPUT_GET, "tomorrow", FILTER_SANITIZE_NUMBER_INT);
    $queries["overdue"] = (int)filter_input(INPUT_GET, "overdue", FILTER_SANITIZE_NUMBER_INT);
    $queries["query"] = filter_input(INPUT_GET, "query", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    return $queries;
}

/**
 * Обёртка над всеми запросами задач в базу
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - идентификатор пользователя
 * @param array $queryStringsValues - ассоциативный массив
 * со значениями из GET - запросов
 * @return array|null - массив задач
 */
function getTasksWrapper(mysqli $con, int $userId, array &$queryStringsValues): ?array
{
    if ($queryStringsValues["project_id"]) {
        return getTasksByProjectId($con, $userId, $queryStringsValues["project_id"]);
    }

    if (isset($_GET["query"])) {
        $query = filter_input(INPUT_GET, "query", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return getTasksByQuery($con, $userId, $query);
    }

    if ($queryStringsValues["current_day"] === 1) {
        return getTasksByDate($con, $userId, date_create()->format("Y-m-d"));
    }

    if ($queryStringsValues["tomorrow"] === 1) {
        return getTasksByDate($con, $userId, date_create()->modify("+1 day")->format("Y-m-d"));
    }

    if ($queryStringsValues["overdue"] === 1) {
        return getOverdueTasks($con, $userId, date_create()->format("Y-m-d"));
    }

    $queryStringsValues["all_tasks"] = 1;

    return getTasksAll($con, $userId);
}

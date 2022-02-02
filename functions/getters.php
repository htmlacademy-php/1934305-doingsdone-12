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

    return $queries;
}

/**
 * Обёртка над всеми запросами задач в базу
 * @param mysqli $con - объект подключения к БД
 * @param int $userId - идентификатор пользователя
 * @param array $criteria - ассоциативный массив
 * со значениями из GET - запросов
 * @param bool $showCompleteTasks - значение для отображения
 * законченных задач из БД
 * @return array|null - массив задач
 */
function getTasksWrapper(mysqli $con, int $userId, array $criteria, bool $showCompleteTasks, array $errors): ?array
{
    if ($criteria[PROJECT_ID]) {
        return getTasksByProjectId($con, $userId, $criteria[PROJECT_ID], $showCompleteTasks);
    }

    if (!isset($errors[QUERY]) && isset($_GET[QUERY])) {
        $query = filter_input(INPUT_GET, QUERY, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return getTasksByQuery($con, $userId, $query, $showCompleteTasks);
    }

    if ($criteria["expire"] === CURRENT_DAY) {
        return getTasksByDate($con, $userId, date_create()->format("Y-m-d"), $showCompleteTasks);
    }

    if ($criteria["expire"] === TOMORROW) {
        return getTasksByDate($con, $userId, date_create()->modify("+1 day")->format("Y-m-d"), $showCompleteTasks);
    }

    if ($criteria["expire"] === OVERDUE) {
        return getOverdueTasks($con, $userId, date_create()->format("Y-m-d"));
    }

    return getTasksAll($con, $userId, $showCompleteTasks);
}

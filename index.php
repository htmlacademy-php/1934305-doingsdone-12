<?php

require_once("helpers.php");
require_once("init.php");

$show_complete_tasks = rand(0, 1);
$title = "Дела в порядке";

/**
 * Вычисляет количество задач в каждой из категорий проектов и возвращает результат в виде числа.
 * @param string $category имя категории, которую нужно подсчитать
 * @param array $tasks массив всех задач.
 * @return int количество задач нужной категории
 */
function count_categories(string $category, array $tasks): int
{
    $count = 0;

    foreach ($tasks as $task) {
        if ($category === $task["category"]) {
            $count++;
        }
    }

    return $count;
}

/**
 * Простая фунция-обертка, фильтрует содержимое и возвращает строку, очищенную
 * от опасных спецсимволов
 * @param string|null @str строка с данными, которая может содержать спецсимволы, или null
 * @return string $text очищенная строка
 */
function esc(?string $str): string
{
    return htmlspecialchars($str);
}

/**
 * Определяет является ли дата датой, для выполнения которой осталось меньше 24 часов
 * @param string|null $date_str дата в виде строки так же может быть null
 * @return bool если количество часов до выполнения задачи меньше или равно 24 возвращает true, иначе false
 */
function is_task_important(?string $date_str): bool
{
    if ($date_str === null) {
        return false;
    }

    $dt_end = date_create($date_str);

    // Т.к. в данных у даты не указанны часы, то дата создаётся в часовом диапазоне 00:00
    // но в задаче подразумевается, что дата считается с конца дня, а не с начала, для этого
    // добавляю еще 24 часа к созданной дате.
    $dt_end->modify("+1 day");
    $dt_now = date_create();

    $diff = $dt_now->diff($dt_end);

    $hours = (int)$diff->format("%r%h");
    $hours += (int)$diff->format("%r%a") * 24;

    if ($hours <= 24) {
        return true;
    }

    return false;
}

/**
 * Отображает что произошла ошибка при обработке запроса и выводит стандартный html шаблон
 * @param $error - ошибка от БД
 */
function render_error($error)
{
    $err_content = include_template("error.php", ["error" => $error]);
    $layout_content = include_template("layout.php", [
        "content" => $err_content,
        "title" => "Ошибка соединения"
    ]);

    print($layout_content);
}

/**
 * Возвращает результат работы подготовленного выражения для дальнейшей обраотки данных пользователя
 * @param string $sql_query - подготовленная строка SQL запроса
 * @param string $id - номер id пользователя
 * @param @con - информация для соединения с БД
 * @return mysqli_result - результат подготовленного выражения
 */
function get_user_stmt_result(string $sql_query, string $id, $con): mysqli_result
{
    $prepared_statement = db_get_prepare_stmt($con, $sql_query, ["id" => $id]);
    mysqli_stmt_execute($prepared_statement);

    return mysqli_stmt_get_result($prepared_statement);
}

// TODO: вынести соединение в функцию установить кодировку после проверки соединения
$con = mysqli_connect($config["db"]["host"], $config["db"]["user"], $config["db"]["password"], $config["db"]["database"]);
mysqli_set_charset($con, "utf8");

if ($con === false) {
    $error = mysqli_connect_error();
    render_error($error);
    exit();
}

// TODO: вынести внутри функцию
$select_projects_by_id = "SELECT name FROM projects WHERE user_id = ?";
$select_tasks_by_id =
    "SELECT t.name AS task_name, t.end_time AS date, p.name AS category, t.status AS is_finished
    FROM tasks AS t
    JOIN projects AS p ON t.project_id = p.id
    WHERE t.user_id = ?";
$id = 1; // Сейчас пока 1, потом заменю на $_GET
$categories = [];

// TODO: $projects = get_projects($con, $id);
$result = get_user_stmt_result($select_projects_by_id, $id, $con);
if ($result) {
    // TODO: ассоциативный массив с id и name
    $data_res = mysqli_fetch_all($result, MYSQLI_NUM);
    foreach ($data_res as $elem) {
        $categories = array_merge($categories, $elem);
    }
} else {
    $error = mysqli_error($con);
    render_error($error);
    exit();
}

// TODO: $tasks = get_tasks($con, $id);
$result = get_user_stmt_result($select_tasks_by_id, $id, $con);
if ($result) {
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $error = mysqli_error($con);
    render_error($error);
    exit();
}


$page_content = include_template("main.php", [
    "categories" => $categories,
    "tasks" => $tasks,
    "show_complete_tasks" => $show_complete_tasks
]);

$layout_content = include_template("layout.php", [
    "content" => $page_content,
    "title" => $title
]);

print($layout_content);

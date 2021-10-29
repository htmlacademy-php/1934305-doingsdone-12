<?php
require_once("helpers.php");

$show_complete_tasks = rand(0, 1);
$title = "Дела в порядке";

$categories = ["Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];

$tasks = [
    [
        "task_name" => "Собеседование в IT компании",
        "date" => "01.11.2021",
        "category" => "Работа",
        "is_finished" => false
    ],

    [
        "task_name" => "Выполнить тестовое задание",
        "date" => "29.10.2021",
        "category" => "Работа",
        "is_finished" => false
    ],

    [
        "task_name" => "Сделать задание первого раздела",
        "date" => "21.11.2021",
        "category" => "Учеба",
        "is_finished" => true
    ],

    [
        "task_name" => "Встреча с другом",
        "date" => "22.11.2021",
        "category" => "Входящие",
        "is_finished" => false
    ],

    [
        "task_name" => "Купить корм для кота",
        "date" => null,
        "category" => "Домашние дела",
        "is_finished" => false
    ],

    [
        "task_name" => "Заказать пиццу",
        "date" => null,
        "category" => "Домашние дела",
        "is_finished" => false
    ],

];

/**
* Вычисляет количество задач в каждой из категорий проектов и возвращает результат в виде числа.
* @param string $category имя категории, которую нужно подсчитать
* @param array $task массив всех задач.
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
* @param @str строка с данными, которая может содержать спецсимволы, или null
* @return $text очищенная строка
*/
function esc(string|null $str): string 
{
	$text = htmlspecialchars($str);

	return $text;
}

/**
* Определяет является ли дата датой, для выполнения которой осталось меньше 24 часов
* @param string $date_str дата в виде строки так же может быть null
* @return bool если количество часов до выполнения задачи меньше или равно 24 возвращает true, иначе false
*/
function is_task_important(string|null $date_str): bool
{
    if ($date_str === null) {
        return false;
    }

    $dt_end = date_create($date_str);

    // Т.к. в данных у даты не указанны часы, то дата создаётся в часовом диапазоне 00:00
    // но в задаче подразумевается, что дата считается с конца дня, а не с начала, для этого
    // добавляю еще 24 часа к созданной дате.
    $dt_end->modify("+1 day");
    $dt_now = date_create("now");

    $diff = $dt_now->diff($dt_end);

    $hours = (int)$diff->format("%r%h");
    $hours += (int)$diff->format("%r%a") * 24;

    if ($hours <= 24) {
        return true;
    }

    return false;
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
?>

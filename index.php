<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("helpers.php");
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$title = "Дела в порядке";

$categories = ["Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];

$tasks = [
    [
        "task_name" => "Собеседование в IT компании",
        "date" => "01.12.2019",
        "category" => "Работа",
        "is_finished" => false
    ],

    [
        "task_name" => "Выполнить тестовое задание",
        "date" => "25.12.2019",
        "category" => "Работа",
        "is_finished" => false
    ],

    [
        "task_name" => "Сделать задание первого раздела",
        "date" => "21.12.2019",
        "category" => "Учеба",
        "is_finished" => true
    ],

    [
        "task_name" => "Встреча с другом",
        "date" => "22.12.2019",
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
* @param @str строка с данными, которая может содержать спецсимволы
* @return $text очищенная строка
*/
function esc($str) {
	$text = htmlspecialchars($str);

	return $text;
}

$page_content = include_template("main.php", [
    "categories" => $categories,
    "tasks" => $tasks,
    "count_categories" => "count_categories",
    "show_complete_tasks" => $show_complete_tasks,
    "esc" => "esc"
]);

$layout_content = include_template("layout.php", [
    "content" => $page_content,
    "title" => $title
]);

print($layout_content);
?>

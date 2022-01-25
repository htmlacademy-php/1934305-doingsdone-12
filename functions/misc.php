<?php

/**
 * Получает значение из массива $_POST
 * по ключу $name фильтрует и возвращает результат
 * @param string $name - ключ по котороу нужно получить значение из массива
 * @return string - значение из массива $_POST
 */
function getPostVal(string $name): string
{
    return (string)filter_input(INPUT_POST, $name);
}

/**
 * Сохраняет состояние завершённых задач в сессии
 */
function saveCompleteTasksToSession()
{
    if (!isset($_SESSION["show_complete_tasks"])) {
        $_SESSION["show_complete_tasks"] = 1;
    }

    if (isset($_GET["show_completed"])) {
        $_SESSION["show_complete_tasks"] = filter_input(INPUT_GET, "show_completed", FILTER_SANITIZE_NUMBER_INT);
    }
}

<?php

/**
 * Получает значение из массива $_POST по ключу $name фильтрует и возвращает результат
 * @param string $name - ключ по котороу нужно получить значение из массива
 * @return string - значение из массива $_POST
 */
function getPostVal(string $name): string
{
    return (string) filter_input(INPUT_POST, $name);
}

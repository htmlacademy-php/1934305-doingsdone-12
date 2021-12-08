<?php

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function includeTemplate(string $name, array $data = []): string
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
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
 * Отображает что произошла ошибка при обработке запроса и выводит стандартный html шаблон
 * @param $error - ошибка от БД
 */
function renderError($error)
{
    $errContent = includeTemplate("error.php", ["error" => $error]);
    $layoutContent = includeTemplate("layout.php", [
        "content" => $errContent,
        "title" => "Ошибка соединения",
        "addScript" => pathinfo("add.php", PATHINFO_BASENAME)
    ]);

    print($layoutContent);
}

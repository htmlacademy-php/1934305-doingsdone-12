<?php

/**
 * Подключает шаблон, передает
 * туда данные и возвращает итоговый HTML контент
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
 * Простая фунция-обертка,
 * фильтрует содержимое и возвращает строку, очищенную
 * от опасных спецсимволов
 * @param string|null @str строка с данными,
 * которая может содержать спецсимволы, или null
 * @return string $text очищенная строка
 */
function esc(?string $str): string
{
    return htmlspecialchars($str);
}

/**
 * Отображает что произошла ошибка при
 * обработке запроса и выводит стандартный html шаблон
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

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому
 * вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного
 * числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function getNounPluralForm(int $number, string $one, string $two, string $many): string
{
    $number = (int)$number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

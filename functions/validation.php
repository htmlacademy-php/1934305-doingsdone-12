<?php

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * isDateValid('2019-01-01'); // true
 * isDateValid('2016-02-29'); // true
 * isDateValid('2019-04-31'); // false
 * isDateValid('10.10.2010'); // false
 * isDateValid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function isDateValid(string $date): bool
{
    $formatToCheck = 'Y-m-d';
    $dateTimeObj = date_create_from_format($formatToCheck, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
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
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
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

/**
 * Определяет является ли дата датой, для выполнения которой осталось меньше 24 часов
 * @param string|null $dateStr дата в виде строки так же может быть null
 * @param DateTime $dtNow текущая дата
 * @return bool если количество часов до выполнения задачи меньше или равно 24 возвращает true, иначе false
 */
function isTaskImportant(?string $dateStr, DateTime $dtNow): bool
{
    if ($dateStr === null) {
        return false;
    }

    $dtEnd = date_create($dateStr);

    // Т.к. в данных у даты не указанны часы, то дата создаётся в часовом диапазоне 00:00
    // но в задаче подразумевается, что дата считается с конца дня, а не с начала, для этого
    // добавляю еще 24 часа к созданной дате.
    $dtEnd->modify("+1 day");

    $diff = $dtNow->diff($dtEnd);

    $hours = (int)$diff->format("%r%h");
    $hours += (int)$diff->format("%r%a") * 24;

    if ($hours <= 24) {
        return true;
    }

    return false;
}

/**
 * Вычисляет количество задач в каждой из категорий проектов и возвращает результат в виде числа.
 * @param string $project имя категории, которую нужно подсчитать
 * @param array $tasks массив всех задач.
 * @return int количество задач нужной категории
 */
function countProjects(string $project, array $tasks): int
{
    $count = 0;

    foreach ($tasks as $task) {
        if ($project === $task["project"]) {
            $count++;
        }
    }

    return $count;
}

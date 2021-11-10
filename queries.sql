/*
 Создание пользователей 
*/

INSERT INTO users(
    name, 
    email, 
    password, 
    registration_time
)
    VALUES
        ("Дмитрий Иванов", "d_ivanov@mail.ru", "123456789", NOW()),
        ("Анатолий Белый", "a_beliy@mail.ru", "123456789", NOW() + INTERVAL 10 HOUR);


/*
 Заполнение таблицы проектов проектами 
*/

INSERT INTO projects(name, user_id)
    VALUES
        ("Входящие", 1),
        ("Учеба", 1),
        ("Работа", 1),
        ("Домашние дела", 2),
        ("Авто", 2);


/*
 Заполнение таблицы задачами
*/

INSERT INTO tasks(
    name, 
    creation_time, 
    status, 
    file, 
    end_time, 
    user_id, 
    project_id
)
    VALUES
        ("Собеседование в IT компании", NOW(), false, "", "2021-12-1", 1, 3),
        ("Выполнить тестовое задание", NOW() + INTERVAL 1 HOUR, false, "", "2021-11-29", 1, 3),
        ("Сделать задание первого раздела", NOW() + INTERVAL 2 HOUR, true, "", "2021-11-21", 1, 2),
        ("Встреча с другом", NOW() + INTERVAL 3 HOUR, false, "", "2021-11-22", 1, 1),
        ("Купить корм для кота", NOW() + INTERVAL 4 HOUR, false, "", NULL, 2, 4),
        ("Заказать пиццу", NOW() + INTERVAL 5 HOUR, false, "", NULL, 2, 4);


/*
 получить список из всех проектов для одного пользователя
*/

SELECT 
    p.* 
FROM 
    projects AS p 
JOIN users AS u ON p.user_id = u.id 
WHERE u.name = "Дмитрий Иванов";


/*
 получить список из всех задач для одного проекта
*/

SELECT 
    t.* 
FROM 
    tasks AS t 
JOIN projects AS p ON t.project_id = p.id 
WHERE p.name = "Домашние дела";

/*
 пометить задачу как выполненную 
*/

UPDATE tasks SET status = True WHERE tasks.id = 2;

/*
 обновить название задачи по её идентификатору 
*/

UPDATE tasks SET name = "Поесть пиццы" WHERE tasks.id = 2;

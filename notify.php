<?php

/* @var mysqli $con
 * @var string $dsn
 */

require_once "init.php";

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$date = date_create()->format("Y-m-d");
$usersData = getUsersTasksByDate($con, $date);
$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);

foreach ($usersData as $userData) {
    $message = new Email();
    $message->to($userData["email"]);
    $message->from("mail@doingsdone.site");
    $message->subject("Уведомление от сервиса «Дела в порядке»");

    // Если под ключом task_names хранится
    // строка с множеством задач. То они
    // разделяются запятой, следовательно
    // предложение выбирается в множественном
    // числе, и в единственном, если запятых в строке нет.
    $plannedTaskString =
        (mb_strpos($userData["tasks_names"], ",")) ?
            "У вас запланированны задачи "
            : "У вас запланирована задача ";

    $message->text(
        "Уважаемый, " . $userData["user_name"] . ". "
        . $plannedTaskString
        . $userData["tasks_names"]
        . " на " . $userData["date"] . "."
    );

    $mailer->send($message);
}

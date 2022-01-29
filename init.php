<?php

/* Constants */
const ADD_SCRIPT = "add.php";
const LOGOUT_SCRIPT = "logout.php";
const AUTH_SCRIPT = "auth.php";
const REGISTER_SCRIPT = "register.php";
const PROJECT_SCRIPT = "project.php";
const PROJECT_ID = "project_id";
const TASK_ID = "task_id";
const CURRENT_DAY = "current_day";
const TOMORROW = "tomorrow";
const OVERDUE = "overdue";
const QUERY = "query";
const ALL_TASKS = "all_tasks";

session_start();
require_once __DIR__ . "/vendor/autoload.php";
$config = require_once "config.php";

require_once "functions/url.php";
require_once "functions/database.php";
require_once "functions/validation.php";
require_once "functions/templates.php";
require_once "functions/misc.php";
require_once "functions/getters.php";
require_once "functions/makers.php";
require_once "functions/file.php";

$con = makeConnection($config["db"]);
$dsn = makeDsn($config["mailer"]);

$title = "Дела в порядке";
$userId = 0;

if (isset($_SESSION["user"])) {
    $userId = $_SESSION["user"]["id"];
}

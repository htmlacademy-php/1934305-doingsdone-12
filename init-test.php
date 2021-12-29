<?php
session_start();
require_once __DIR__ . "/vendor/autoload.php";
$config = require_once "config-test.php";

require_once "Database.php";
require_once "functions/url.php";
require_once "functions/database.php";
require_once "functions/validation.php";
require_once "functions/templates.php";
require_once "functions/misc.php";

Database::$con = makeConnection($config["db"]);
$title = "Дела в порядке";

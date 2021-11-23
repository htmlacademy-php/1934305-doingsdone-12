<?php
$config = require_once("config.php");

require_once("functions/url.php");
require_once("functions/database.php");
require_once("functions/validation.php");
require_once("functions/templates.php");

$con = makeConnection($config["db"]);

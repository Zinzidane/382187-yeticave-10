<?php
session_start();

require_once('helpers.php');
require_once('functions.php');
require_once('config/db.php');

date_default_timezone_set('Europe/Moscow');

$link = mysqli_connect($connection['host'], $connection['user'], $connection['password'], $connection['database']);

mysqli_set_charset($link, "utf8");

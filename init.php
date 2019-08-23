<?php
require_once('helpers.php');
require_once('functions.php');
require_once('config/db.php');

$link = mysqli_connect($connection['host'], $connection['user'], $connection['password'], $connection['database']);

mysqli_set_charset($link, "utf8");

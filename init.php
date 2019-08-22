<?php
require_once 'functions.php';
require_once 'config/db.php';

$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

mysqli_set_charset($link, "utf8");
$is_auth = rand(0, 1);

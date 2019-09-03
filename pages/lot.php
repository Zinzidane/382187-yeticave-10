<?php
require_once('../init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

if (isset($_GET['id'])) {
    $lot_id = mysqli_real_escape_string($link, $_GET['id']);
    $categories_sql = 'SELECT name, symbol_code FROM category';
    $lot_sql = 'SELECT lot.title, lot.initial_rate, lot.rate_step, lot.image, lot.date_close, MAX(bet.rate) AS current_rate, category.name AS category, COUNT(bet.lot_id) AS bets_number FROM lot '
    . 'JOIN category ON lot.category_id = category.id '
    . 'JOIN bet ON lot.id = bet.lot_id '
    . 'WHERE lot.id = ' . $lot_id;
    $bets_sql = 'SELECT bet.rate as rate, bet.date_add as date_add, user.name as user FROM lot '
    . 'JOIN bet ON lot.id = bet.lot_id '
    . 'JOIN user on user.id = bet.user_id '
    . 'WHERE lot.id = ' . $lot_id
    . ' ORDER BY lot.date_add DESC';


    $categories_result = mysqli_query($link, $categories_sql);
    $lot_result = mysqli_query($link, $lot_sql);
    $bets_result = mysqli_query($link, $bets_sql);

    if (!$categories_result || !$lot_result || !$bets_result) {
        $error = mysqli_error($link);
        header("HTTP/1.0 404 Not Found");
    }
    $is_auth = rand(0, 1);
    $categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);
    $lot = mysqli_fetch_all($lot_result, MYSQLI_ASSOC)[0];
    $bets = mysqli_fetch_all($bets_result, MYSQLI_ASSOC);
}
else {
    header("HTTP/1.0 404 Not Found");
}

$page_content = include_template('lot.php', ['lot' => $lot, 'bets' => $bets, 'is_auth' => isset($_SESSION['user'])]);
$layout_content = include_template('layout.php', [
    'title' => 'Главная',
    'username' => isset($_SESSION['user']) ? $_SESSION['user']['name'] : null,
    'is_auth' => isset($_SESSION['user']),
    'content' => $page_content,
    'categories' => $categories
]);

print($layout_content);

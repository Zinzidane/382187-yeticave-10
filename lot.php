<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

if (isset($_GET['id'])) {
    $lot_id = $_GET['id'];
    $categories_sql = 'SELECT name, symbol_code FROM category';
    $categories_result = mysqli_query($link, $categories_sql);

    $lot_sql = `SELECT lot.title, lot.initial_rate, lot.rate_step, lot.image, lot.date_close, MAX(bet.rate) AS current_rate, category.name AS category, COUNT(bet.lot_id) AS bets_number FROM lot 
    JOIN category ON lot.category_id = category.id 
    JOIN bet ON lot.id = bet.lot_id 
    WHERE lot.id =  ?`;
    $lot_stmt = db_get_prepare_stmt($link, $lot_sql, [$lot_id]);
    $lot_res = mysqli_stmt_execute($lot_stmt);

    $bets_sql = `SELECT bet.rate as rate, bet.date_add as date_add, user.name as user FROM lot 
    JOIN bet ON lot.id = bet.lot_id 
    JOIN user on user.id = bet.user_id 
    WHERE lot.id = ? 
    ORDER BY lot.date_add DESC`;
    $bets_stmt = db_get_prepare_stmt($link, $bets_sql, [$lot_id]);
    $bet_res = mysqli_stmt_execute($bets_stmt);

    if (!$categories_result || !$lot_result || !$bets_result) {
        $error = mysqli_error($link);
        header("HTTP/1.0 404 Not Found");
    }

    $categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);
    $lot = mysqli_fetch_assoc(mysqli_stmt_get_result($lot_stmt));
    $bets = mysqli_fetch_assoc(mysqli_stmt_get_result($bets_stmt));
}
else {
    header("HTTP/1.0 404 Not Found");
}

$page_content = include_template('lot.php', ['lot' => $lot, 'bets' => $bets, 'is_auth' => is_auth()]);
$layout_content = include_template('layout.php', [
    'title' => 'Главная',
    'username' => get_username(),
    'is_auth' => is_auth(),
    'content' => $page_content,
    'categories' => $categories
]);

print($layout_content);

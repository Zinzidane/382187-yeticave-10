<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

if (isset($_GET['id'])) {
    $lot_id = mysqli_real_escape_string($link, $_GET['id']);
    $lot_sql = 'SELECT lot.title, lot.user_id, lot.initial_rate, lot.rate_step, lot.image, lot.date_close, MAX(bet.rate) AS current_rate, category.name AS category, COUNT(bet.lot_id) AS bets_number FROM lot '
    . 'JOIN category ON lot.category_id = category.id '
    . 'JOIN bet ON lot.id = bet.lot_id '
    . 'WHERE lot.id = ' . $lot_id;
    $lot_result = mysqli_query($link, $lot_sql);

    if (!$lot_result) {
        $error = mysqli_error($link);
        header("HTTP/1.0 404 Not Found");
    }
    $categories = getCategories($link);
    $lot = mysqli_fetch_all($lot_result, MYSQLI_ASSOC)[0];
    $bets = getBets($link, $lot_id);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['cost']) {
        $bet = (int) htmlspecialchars($_POST['cost'], ENT_QUOTES);
        $errors = validateBetForm($bet, $lot);

        if (count($errors)) {
            $page_content = include_template('lot.php', ['lot' => $lot, 'bets' => $bets, 'is_auth' => isAuth(), 'errors' => $errors]);
        } else {
            $rate_sql = 'INSERT INTO bet (rate, user_id, lot_id) VALUES (?, ?, ?)';
            $rate_stmt = db_get_prepare_stmt($link, $rate_sql, [$_POST['cost'], getUserId(), $lot_id]);
            $rate_res = mysqli_stmt_execute($rate_stmt);

            if ($rate_res) {
                $updated_bets = getBets($link, $lot_id);
                $page_content = include_template('lot.php', ['lot' => $lot, 'bets' => $updated_bets, 'is_auth' => isAuth()]);
            } else {
                print('Проблема с добавление лота в базу данных.');
                exit;
            }
        }
    } else {
        $page_content = include_template('lot.php', ['lot' => $lot, 'bets' => $bets, 'is_auth' => isAuth(), 'errors' => $errors]);
    }
} else {
    header("HTTP/1.0 404 Not Found");
}

$layout_content = include_template('layout.php', [
    'title' => 'Просмотр лота',
    'username' => getUsername(),
    'is_auth' => isAuth(),
    'content' => $page_content,
    'categories' => $categories
]);

print($layout_content);

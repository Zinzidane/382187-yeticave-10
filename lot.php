<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

if (!isset($_GET['id'])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

$lotId = mysqli_real_escape_string($link, htmlspecialchars($_GET['id'], ENT_QUOTES));
$lotSql = 'SELECT lot.title, lot.description, lot.user_id, lot.initial_rate, lot.rate_step, lot.image, lot.date_close, MAX(bet.rate) AS current_rate, category.name AS category, COUNT(bet.lot_id) AS bets_number FROM lot '
. 'JOIN category ON lot.category_id = category.id '
. 'JOIN bet ON lot.id = bet.lot_id '
. 'WHERE lot.id = ' . $lotId;
$lotResult = mysqli_query($link, $lotSql);

if (!$lotResult) {
    $error = mysqli_error($link);
    header("HTTP/1.0 404 Not Found");
}

$categories = getCategories($link);
$lot = mysqli_fetch_all($lotResult, MYSQLI_ASSOC)[0];
$bets = getBets($link, $lotId);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['cost']) {
    $bet = (int) htmlspecialchars($_POST['cost'], ENT_QUOTES);
    $errors = validateBetForm($bet, $lot);

    if (count($errors)) {
        $pageContent = include_template('lot.php', ['lot' => $lot, 'bets' => $bets, 'isAuth' => isAuth(), 'errors' => $errors]);
    } else {
        $rateSql = 'INSERT INTO bet (rate, user_id, lot_id) VALUES (?, ?, ?)';
        $rateStmt = db_get_prepare_stmt($link, $rateSql, [$_POST['cost'], getUserId(), $lotId]);
        $rateRes = mysqli_stmt_execute($rateStmt);

        if ($rateRes) {
            $updatedBets = getBets($link, $lotId);
            $pageContent = include_template('lot.php', ['lot' => $lot, 'bets' => $updatedBets, 'isAuth' => isAuth()]);
        } else {
            print('Проблема с добавление лота в базу данных.');
            exit;
        }
    }
} else {
    $pageContent = include_template('lot.php', ['lot' => $lot, 'bets' => $bets, 'isAuth' => isAuth(), 'errors' => $errors]);
}

$layoutContent = include_template('layout.php', [
    'title' => 'Просмотр лота',
    'username' => getUsername(),
    'isAuth' => isAuth(),
    'content' => $pageContent,
    'categories' => $categories
]);

print($layoutContent);

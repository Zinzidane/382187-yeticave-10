<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

$categories = getCategories($link);

$lotsSql = "
SELECT bet.user_id, bet.id, lot.title, lot.date_close, lot.image, lot.description, category.name as category, bet.rate, bet.lot_id, lot.winner_id, bet.date_add, lot.date_close
FROM bet
INNER JOIN lot ON lot.id = bet.lot_id
LEFT JOIN category ON lot.category_id = category.id
WHERE bet.user_id = ?
ORDER BY bet.date_add DESC
";
$lotsStmt = db_get_prepare_stmt($link, $lotsSql, [getUserId()]);
$lotsRes = mysqli_stmt_execute($lotsStmt);

if (!$lotsRes) {
    $error = mysqli_error($link);
    print('Возникла проблема. Попробуйте еще раз.');
}


$lots = mysqli_fetch_all(mysqli_stmt_get_result($lotsStmt), MYSQLI_ASSOC);

$pageContent = include_template('my-bets.php', ['bets' => $lots]);
$layoutContent = include_template('layout.php', [
    'title' => 'Мои ставки',
    'username' => getUsername(),
    'isAuth' => isAuth(),
    'content' => $pageContent,
    'categories' => $categories
]);

print($layoutContent);


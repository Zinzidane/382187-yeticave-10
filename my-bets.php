<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

$lots_sql = "
SELECT bet.user_id, bet.id, lot.title, lot.date_close, lot.image, lot.description, category.name as category, bet.rate, bet.lot_id, lot.winner_id, bet.date_add, lot.date_close
FROM bet
INNER JOIN lot ON lot.id = bet.lot_id
LEFT JOIN category ON lot.category_id = category.id
WHERE bet.user_id = ?
ORDER BY bet.date_add DESC;
";
$lots_stmt = db_get_prepare_stmt($link, $lots_sql, [get_user_id()]);
$lots_res = mysqli_stmt_execute($lots_stmt);

if (!$lots_res) {
    $error = mysqli_error($link);
    print('Возникла проблема. Попробуйте еще раз.');
}

$categories = get_categories($link);
$lots = mysqli_fetch_all(mysqli_stmt_get_result($lots_stmt), MYSQLI_ASSOC);

$page_content = include_template('my-bets.php', ['categories' => $categories, 'bets' => $lots]);
$layout_content = include_template('layout.php', [
    'title' => 'Мои ставки',
    'username' => get_username(),
    'is_auth' => is_auth(),
    'content' => $page_content,
    'categories' => $categories
]);

print($layout_content);


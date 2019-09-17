<?
require_once('vendor/autoload.php');
require_once('init.php');
require_once('getwinner.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

// $categories_sql = 'SELECT name, symbol_code FROM category';
// $categories_result = mysqli_query($link, $categories_sql);
$lots_sql = 'SELECT lot.id, lot.title, lot.initial_rate, lot.image, lot.date_close, category.name AS category FROM lot '
. 'JOIN category ON lot.category_id = category.id '
. 'WHERE lot.date_close > NOW() AND lot.winner_id IS NULL '
. 'GROUP BY lot.id '
. 'ORDER BY lot.date_add DESC';

$lots_result = mysqli_query($link, $lots_sql);

if (!$lots_result) {
    $error = mysqli_error($link);
    print('Возникла проблема. Попробуйте еще раз.');
}

$categories = get_categories($link);
$lots = mysqli_fetch_all($lots_result, MYSQLI_ASSOC);

$page_content = include_template('main.php', ['categories' => $categories, 'lots' => $lots]);
$layout_content = include_template('layout.php', [
    'title' => 'Главная',
    'username' => get_username(),
    'is_auth' => is_auth(),
    'content' => $page_content,
    'categories' => $categories
]);

print($layout_content);


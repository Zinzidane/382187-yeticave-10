<?
require_once('vendor/autoload.php');
require_once('init.php');
require_once('getwinner.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

$lotsSql = 'SELECT lot.id, lot.title, lot.initial_rate, lot.image, lot.date_close, category.name AS category FROM lot '
. 'JOIN category ON lot.category_id = category.id '
. 'WHERE lot.date_close > NOW() AND lot.winner_id IS NULL '
. 'GROUP BY lot.id '
. 'ORDER BY lot.date_add DESC';

$lotsResult = mysqli_query($link, $lotsSql);

if (!$lotsResult) {
    $error = mysqli_error($link);
    print('Возникла проблема. Попробуйте еще раз.');
}

$categories = getCategories($link);
$lots = mysqli_fetch_all($lotsResult, MYSQLI_ASSOC);

$pageContent = include_template('main.php', ['categories' => $categories, 'lots' => $lots]);
$layoutContent = include_template('layout.php', [
    'title' => 'Главная',
    'username' => getUsername(),
    'isAuth' => isAuth(),
    'content' => $pageContent,
    'categories' => $categories
]);

print($layoutContent);


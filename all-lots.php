<?php
require_once('init.php');

$is_auth = is_auth();
if (!isset($_GET['category'])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
if ($link) {
    $category = htmlspecialchars($_GET['category'], ENT_QUOTES);
    $cur_page = intval($_GET['page'] ?? 1);
    $page_items = 9;

    $categories = get_categories($link);
    // $categories_sql = 'SELECT name, symbol_code FROM category';
    // $categories_result = mysqli_query($link, $categories_sql);
    // $categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);

    $sql = "
    SELECT lot.id, lot.title, category.name as category
    FROM lot
    INNER JOIN category ON lot.category_id = category.id
    WHERE lot.date_close > NOW() AND category.name = ?;
    ";;
    $stmt = db_get_prepare_stmt($link, $sql, [$category]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $items_count = count($lots);
    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;
    $pages = range(1, $pages_count);
    $sql_page = "
    SELECT lot.id, lot.title, lot.initial_rate, lot.date_close, lot.image, category.name as category, COUNT(bet.lot_id) as betsCount, MAX(bet.rate) + lot.initial_rate as betsPrice
    FROM lot
    INNER JOIN category ON lot.category_id = category.id
    LEFT JOIN bet ON lot.id = bet.lot_id
    WHERE lot.date_close > NOW() AND category.name = ?
    GROUP BY lot.title, lot.initial_rate, lot.date_close, lot.image, category.name, lot.date_add, lot.id
    ORDER BY lot.date_add DESC LIMIT ? OFFSET ?;
    ";;
    $stmt = db_get_prepare_stmt($link, $sql_page, [$category, $page_items, $offset]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
}
foreach ($lots as $i => $array) {
    foreach ($array as $key => &$value) {
        if (is_string($value)) {
            $lots[$i][$key] = htmlspecialchars($value, ENT_QUOTES);
        }
    }
}

$page_content = include_template('all-lots.php', [
    'lots' => $lots,
    'category' => $category,
    'categories' => $categories,
    'is_auth' => is_auth(),
    'pages_count' => $pages_count,
    'cur_page' => $cur_page,
    'pages' => $pages
]);
$layout_content = include_template('layout.php', [
    'title' => 'Лоты по категориям',
    'username' => get_username(),
    'is_auth' => is_auth(),
    'content' => $page_content,
    'categories' => $categories
]);

print($layout_content);

<?php
require_once('init.php');

$is_auth = is_auth();
if (!isset($_GET['search'])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
if ($link) {
    $search = trim(htmlspecialchars($_GET['search'], ENT_QUOTES));
    $cur_page = intval($_GET['page'] ?? 1);
    $page_items = 9;
    $sql = "
    SELECT lot.id, lot.title, lot.description
    FROM lot
    WHERE lot.date_close > NOW() AND MATCH(lot.title, lot.description) AGAINST(?)
    ";
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $items_count = count($lots);
    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;
    $pages = range(1, $pages_count);
    $sql_page =  "
    SELECT lot.id, lot.title, lot.initial_rate, lot.date_close, lot.image, category.name as category, COUNT(bet.lot_id) as betCount, MAX(bet.rate) + lot.initial_rate as betsPrice
    FROM lot
    INNER JOIN category ON lot.category_id = category.id
    LEFT JOIN bet ON lot.id = bet.lot_id
    WHERE lot.date_close > NOW() AND MATCH(lot.title, lot.description) AGAINST(?)
    GROUP BY lot.title, lot.initial_rate, lot.date_close, lot.image, category.name, lot.date_close, lot.id
    ORDER BY lot.date_close DESC LIMIT ? OFFSET ?;
    ";
    $stmt = db_get_prepare_stmt($link, $sql_page, [$search, $page_items, $offset]);
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

$page_content = include_template('search.php', [
    'lots' => $lots,
    'search' => $search,
    'is_auth' => is_auth(),
    'pages_count' => $pages_count,
    'cur_page' => $cur_page,
    'pages' => $pages
]);

$layout_content = include_template('layout.php', [
    'title' => 'Результаты поиска',
    'username' => get_username(),
    'is_auth' => is_auth(),
    'content' => $page_content,
    'categories' => []
]);

print($layout_content);

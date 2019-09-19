<?php
require_once('init.php');

$isAuth = isAuth();
if (!isset($_GET['search'])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
if ($link) {
    $search = trim(htmlspecialchars($_GET['search'], ENT_QUOTES));
    $curPage = intval($_GET['page'] ?? 1);
    $pageItems = 9;
    $sql = "
    SELECT lot.id, lot.title, lot.description
    FROM lot
    WHERE lot.date_close > NOW() AND MATCH(lot.title, lot.description) AGAINST(?)
    ";
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lots = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $itemsCount = count($lots);
    $pagesCount = ceil($itemsCount / $pageItems);
    $offset = ($curPage - 1) * $pageItems;
    $pages = range(1, $pagesCount);
    $sqlPage =  "
    SELECT lot.id, lot.title, lot.initial_rate, lot.date_close, lot.image, category.name as category, COUNT(bet.lot_id) as betCount, MAX(bet.rate) + lot.initial_rate as betsPrice
    FROM lot
    INNER JOIN category ON lot.category_id = category.id
    LEFT JOIN bet ON lot.id = bet.lot_id
    WHERE lot.date_close > NOW() AND MATCH(lot.title, lot.description) AGAINST(?)
    GROUP BY lot.title, lot.initial_rate, lot.date_close, lot.image, category.name, lot.date_close, lot.id
    ORDER BY lot.date_close DESC LIMIT ? OFFSET ?;
    ";
    $stmt = db_get_prepare_stmt($link, $sqlPage, [$search, $pageItems, $offset]);
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

$pageContent = include_template('search.php', [
    'lots' => $lots,
    'search' => $search,
    'isAuth' => isAuth(),
    'pages_count' => $pagesCount,
    'cur_page' => $curPage,
    'pages' => $pages
]);

$layoutContent = include_template('layout.php', [
    'title' => 'Результаты поиска',
    'username' => getUsername(),
    'isAuth' => isAuth(),
    'content' => $pageContent,
    'categories' => []
]);

print($layoutContent);

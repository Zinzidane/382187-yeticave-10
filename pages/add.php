<?php
require_once('../init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

$sql = 'SELECT id, name, symbol_code FROM category';
$result = mysqli_query($link, $sql);

if ($result) {
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $categories_ids = array_map('get_id', $categories);
} else {
    $error = mysqli_error($link);
    header("HTTP/1.0 404 Not Found");
}

$page_content = include_template('add.php', ['categories' => $categories]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST;
    $errors = validate_lot_form($lot, $categories_ids);

    if (count($errors)) {
        $page_content = include_template('add.php', ['lot' => $lot, 'errors' => $errors, 'categories' => $categories]);
    } else {
        $lot['image'] =  handle_image_upload($_FILES['lot_image']);
        // На время пока нет авторизации
        $lot['user_id'] = $_SESSION['user']['id'];

        $add_lot_sql = 'INSERT INTO lot (title, category_id, description, initial_rate, rate_step, date_close, image, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $add_lot_sql, $lot);
        $add_lot_res = mysqli_stmt_execute($stmt);

        if ($add_lot_res) {
            $lot_id = mysqli_insert_id($link);
            header("Location: lot.php?id=" . $lot_id);
        } else {
            print('Проблема с отправкой данных');
        }
    }
} else {
    if (!isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
    $page_content = include_template('add.php', ['categories' => $categories]);
}

$layout_content = include_template('layout.php', [
    'title' => 'Добавить лот',
    'username' => isset($_SESSION['user']) ? $_SESSION['user']['name'] : null,
    'is_auth' => isset($_SESSION['user']),
    'content' => $page_content,
    'categories' => $categories
]);

print($layout_content);
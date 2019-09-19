<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

$categories = getCategories($link);

if ($categories) {
    $categoriesIds = array_map('getId', $categories);
} else {
    $error = mysqli_error($link);
    header("HTTP/1.0 404 Not Found");
}

$pageContent = include_template('add.php', ['categories' => $categories]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = array_map('htmlspecialchars', $_POST);
    $errors = validateLotForm($lot, $categoriesIds);

    if (count($errors)) {
        $pageContent = include_template('add.php', ['lot' => $lot, 'errors' => $errors, 'categories' => $categories]);
    } else {
        if(!isset($lot['image'])) {
            $lot['image'] =  handleImageUpload($_FILES['lot_image']);
        }

        $lot['user_id'] = getUserId();

        $addLotSql = 'INSERT INTO lot (title, category_id, description, initial_rate, rate_step, date_close, image, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $addLotSql, $lot);
        $addLotRes = mysqli_stmt_execute($stmt);

        if ($addLotRes) {
            $lotId = mysqli_insert_id($link);
            header("Location: lot.php?id=" . $lotId);
        } else {
            print('Проблема с отправкой данных');
        }
    }
} else {
    if (!isAuth()) {
        header("Location: /index.php");
        exit;
    }
    $pageContent = include_template('add.php', ['categories' => $categories]);
}

$layoutContent = include_template('layout.php', [
    'title' => 'Добавить лот',
    'username' => getUsername(),
    'isAuth' => isAuth(),
    'content' => $pageContent,
    'categories' => $categories
]);

print($layoutContent);

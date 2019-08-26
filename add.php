<?php
require_once 'init.php';

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}
else {
    $sql = 'SELECT id, name, symbol_code FROM category';
    $result = mysqli_query($link, $sql);

    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    else {
        $error = mysqli_error($link);
        header("HTTP/1.0 404 Not Found");
    }

    $content = include_template('add.php', ['categories' => $categories]);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $lot = $_POST;

        $filename = uniqid() . '.jpg';
        $lot['image'] = $filename;

        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/uploads/' . $filename);

        $sql = 'INSERT INTO lot (title, category_id, description, initial_rate, rate_step, date_close, image, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, 2)';

        $stmt = db_get_prepare_stmt($link, $sql, $lot);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $lot_id = mysqli_insert_id($link);

            header("Location: lot.php?id=" . $lot_id);
        }
        else {
            print('Проблема с отправкой данных');
        }
    }
}

print include_template('layout.php', [
    'title' => 'Добавить лот',
    'username' => 'Ваня',
    'is_auth' => $is_auth,
    'content' => $content,
    'categories' => $categories
]);

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
        $categories_ids = array_map('get_id', $categories);
    }
    else {
        $error = mysqli_error($link);
        header("HTTP/1.0 404 Not Found");
    }

    $content = include_template('add.php', ['categories' => $categories]);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $lot = $_POST;
        $required = ['title', 'category_id', 'description', 'initial_rate', 'rate_step', 'date_close', 'lot_image'];
        $errors = [];

        $rules = [
            'category_id' => function() use ($categories_ids) {
                return validateCategory('category_id', $categories_ids);
            },
            'title' => function() {
                return validateLength('title', 1, 255);
            },
            'description' => function() {
                return validateLength('description', 0, 255);
            },
            'initial_rate' => function() {
                return  validateRate('initial_rate', 0);
            },
            'rate_step' => function() {
                return  validateRate('rate', 0);
            },
            'date_close' => function() {
                return is_date_valid('date_close');
            }
        ];

        foreach ($_POST as $key => $value) {
            if (isset($rules[$key])) {
                $rule = $rules[$key];
                $errors[$key] = $rule();
            }
        }

        $errors = array_filter($errors);

        foreach ($required as $key) {
            if (empty($_POST[$key])) {
                $errors[$key] = 'Это поле надо заполнить';
            }
        }

        if (isset($_FILES['lot_image']['name'])) {
            $tmp_name = $_FILES['lot_image']['tmp_name'];
            $path = $_FILES['lot_image']['name'];
            $filename = uniqid() . '.jpeg';

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);

            if ($file_type !== "image/jpeg") {
                $errors['file'] = 'Загрузите картинку в формате JPEG';
            }
            else {
                move_uploaded_file($tmp_name, __DIR__ . '/uploads/' . $filename);
                $lot['image'] = $filename;
            }
        }
        else {
            $errors['file'] = 'Вы не загрузили файл';
        }

        if (count($errors)) {
            $page_content = include_template('add.php', ['lot' => $lot, 'errors' => $errors, 'categories' => $categories]);
        }
        else {

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
    else {
        $page_content = include_template('add.php', ['categories' => $categories]);
    }
}


$layout_content = include_template('layout.php', [
    'title' => 'Добавить лот',
    'username' => 'Ваня',
    'is_auth' => $is_auth,
    'content' => $page_content,
    'categories' => $categories
]);

print($layout_content);

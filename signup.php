<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $signup_form = $_POST;
    $errors = validate_signup_form($signup_form);

    if (count($errors)) {
        $page_content = include_template('signup.php', ['signup_form' => $signup_form, 'errors' => $errors]);
    } else {
        $user_find_sql = 'SELECT id FROM user WHERE email = ? ';
        $user_find_stmt = db_get_prepare_stmt($link, $user_find_sql, [$signup_form['email']]);
        $user_find_res = mysqli_stmt_execute($user_find_stmt);

        if (mysqli_num_rows(mysqli_fetch_assoc(mysqli_stmt_get_result($user_find_stmt)))) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
            $page_content = include_template('signup.php', ['signup_form' => $signup_form, 'errors' => $errors]);
        } else {
            $password = password_hash($signup_form['password'], PASSWORD_DEFAULT);

            $user_add_sql = 'INSERT INTO user (email, name, password) VALUES (?, ?, ?)';
            $user_add_stmt = db_get_prepare_stmt($link, $user_add_sql, [$signup_form['email'], $signup_form['name'], $password]);
            $user_add_res = mysqli_stmt_execute($user_add_stmt);
        }

        if ($user_add_res && empty($errors)) {
            header("Location: /signin.php");
            exit;
        }
    }
}
$page_content = include_template('signup.php', ['signup_form' => $signup_form, 'errors' => $errors]);

$layout_content = include_template('layout.php', [
    'title' => 'Yeticave | Регистрация',
    'username' => get_username(),
    'is_auth' => is_auth(),
    'content'=> $page_content,
    'categories' => []
]);

print($layout_content);

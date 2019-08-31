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
        $email = mysqli_real_escape_string($link, $signup_form['email']);
        $user_sql = "SELECT id FROM user WHERE email = '$email'";
        $res = mysqli_query($link, $user_sql);

        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
            $page_content = include_template('signup.php', ['signup_form' => $signup_form, 'errors' => $errors]);
        } else {
            $password = password_hash($signup_form['password'], PASSWORD_DEFAULT);

            $user_sql = 'INSERT INTO user (email, name, password) VALUES (?, ?, ?)';
            $stmt = db_get_prepare_stmt($link, $user_sql, [$signup_form['email'], $signup_form['name'], $password]);
            $res = mysqli_stmt_execute($stmt);
        }

        if ($res && empty($errors)) {
            header("Location: /signin.php");
            exit;
        }
    }
}

$layout_content = include_template('layout.php', [
    'title' => 'Yeticave | Регистрация',
    'username' => 'Ваня',
    'is_auth' => $is_auth,
    'content'=> $page_content,
    'categories' => []
]);

print($layout_content);

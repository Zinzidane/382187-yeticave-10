<?php
require_once('../init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $signin_form = $_POST;
    $errors = validate_signin_form($signin_form);

    if (count($errors)) {
        $page_content = include_template('signin.php', ['signin_form' => $signin_form, 'errors' => $errors]);
    } else {
        $user_login_sql = 'SELECT * FROM user WHERE email = ?';
        $user_login_stmt = db_get_prepare_stmt($link, $user_login_sql, [$signin_form['email']]);
        $user_login_res = mysqli_stmt_execute($user_login_stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($user_login_stmt));

        if (!$user) {
            $errors['email'] = 'Такой пользователь не найден';
            $page_content = include_template('signin.php', ['signin_form' => $signin_form, 'errors' => $errors]);
        } else {
            // var_dump(password_verify($signin_form['password'], $user[0]['password']), $user);
            if (password_verify($signin_form['password'], $user['password'])) {
                $_SESSION['user'] = $user;
            } else {
                $errors['password'] = 'Неверный пароль';
            }
            // $user_add_sql = 'INSERT INTO user (email, name, password) VALUES (?, ?, ?)';
            // $user_add_stmt = db_get_prepare_stmt($link, $user_add_sql, [$signin_form['email'], $signin_form['name'], $password]);
            // $user_add_res = mysqli_stmt_execute($user_add_stmt);
        }

        if ($user && empty($errors)) {
            header("Location: /index.php");
            exit;
        }
    }
} else {
    $page_content = include_template('signin.php', []);

    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
}
$page_content = include_template('signin.php', ['signin_form' => $signin_form, 'errors' => $errors]);

$layout_content = include_template('layout.php', [
    'title' => 'Yeticave | Вход',
    'username' => isset($_SESSION['user']) ? $_SESSION['user']['name'] : null,
    'is_auth' => isset($_SESSION['user']),
    'content'=> $page_content,
    'categories' => []
]);

print($layout_content);

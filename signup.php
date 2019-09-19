<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $signupForm = $_POST;
    $errors = validateSignupForm($signupForm);

    if (count($errors)) {
        $pageContent = include_template('signup.php', ['signup_form' => $signupForm, 'errors' => $errors]);
    } else {
        $userFindSql = 'SELECT id FROM user WHERE email = ? ';
        $userFindStmt = db_get_prepare_stmt($link, $userFindSql, [$signupForm['email']]);
        $userFindRes = mysqli_stmt_execute($userFindStmt);
        $userFind = mysqli_fetch_assoc(mysqli_stmt_get_result($userFindStmt));

        if ($userFind) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
            $pageContent = include_template('signup.php', ['signup_form' => $signupForm, 'errors' => $errors]);
        } else {
            $password = password_hash($signupForm['password'], PASSWORD_DEFAULT);

            $userAddSql = 'INSERT INTO user (email, name, password) VALUES (?, ?, ?)';
            $userAddStmt = db_get_prepare_stmt($link, $userAddSql, [$signupForm['email'], $signupForm['name'], $password]);
            $userAddRes = mysqli_stmt_execute($userAddStmt);
        }

        if ($userAddRes && empty($errors)) {
            header("Location: /signin.php");
            exit;
        }
    }
}
$pageContent = include_template('signup.php', ['signup_form' => $signupForm, 'errors' => $errors]);

$layoutContent = include_template('layout.php', [
    'title' => 'Yeticave | Регистрация',
    'username' => getUsername(),
    'isAuth' => isAuth(),
    'content'=> $pageContent,
    'categories' => []
]);

print($layoutContent);

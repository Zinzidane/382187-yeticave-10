<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $signinForm = $_POST;
    $errors = validateSigninForm($signinForm);

    if (count($errors)) {
        $pageContent = include_template('signin.php', ['signin_form' => $signinForm, 'errors' => $errors]);
    } else {
        $userLoginSql = 'SELECT * FROM user WHERE email = ?';
        $userLoginStmt = db_get_prepare_stmt($link, $userLoginSql, [$signinForm['email']]);
        $userLoginRes = mysqli_stmt_execute($userLoginStmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($userLoginStmt));

        if (!$user) {
            $errors['email'] = 'Такой пользователь не найден';
            $pageContent = include_template('signin.php', ['signin_form' => $signinForm, 'errors' => $errors]);
        } else {
            if (password_verify($signinForm['password'], $user['password'])) {
                $_SESSION['user'] = $user;
            } else {
                $errors['password'] = 'Неверный пароль';
            }
        }

        if ($user && empty($errors)) {
            header("Location: /index.php");
            exit;
        }
    }
} else {
    $pageContent = include_template('signin.php', []);

    if (isAuth()) {
        header("Location: /index.php");
        exit;
    }
}
$pageContent = include_template('signin.php', ['signin_form' => $signinForm, 'errors' => $errors]);

$layoutContent = include_template('layout.php', [
    'title' => 'Yeticave | Вход',
    'username' => getUsername(),
    'isAuth' => isAuth(),
    'content'=> $pageContent,
    'categories' => []
]);

print($layoutContent);

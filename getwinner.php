<?php
require_once('init.php');

if (!$link) {
    $error = mysqli_connect_error();
    print('Проблема с базой данных. Ожидайте пока исправим.');
    exit;
}

$lots_sql = "
SELECT lot.id, lot.title, MAX(bet.rate) as max_bet
FROM lot
INNER JOIN bet ON lot.id = bet.lot_id
WHERE lot.date_close <= NOW() and lot.winner_id IS NULL
GROUP BY lot.id, lot.title;
";
$lots_result = mysqli_query($link, $lots_sql);
$lots = mysqli_fetch_all($lots_result, MYSQLI_ASSOC);

foreach ($lots as $lot) {
    $winner_sql = "
    SELECT bet.user_id, user.name, user.email
    FROM bet
    INNER JOIN user ON bet.user_id = user.id
    WHERE bet.rate = ?;
    ";;
    $stmt = db_get_prepare_stmt($link, $winner_sql, [$lot['max_bet']]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $winner = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $sql = "
    UPDATE lot
    SET winner_id = ?
    WHERE id = ?;
    ";
    $stmt = db_get_prepare_stmt($link, $sql, [$winner[0]['user_id'] , $lot['id']]);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        $body = include_template('email.php', ['lot' => $lot, 'username' => $winner[0]['name']]);

        $transport = (new Swift_SmtpTransport($mail['host'], $mail['port']))
            ->setUsername($mail['username'])
            ->setPassword($mail['password'])
        ;
        $mailer = new Swift_Mailer($transport);
        $message = (new Swift_Message('Ваша ставка победила'))
            ->setFrom([$mail['username']=> $mail['name']])
            ->setTo([$winner[0]['email'] => $winner[0]['name']])
            ->setBody($body, 'text/html')
        ;
        $result = $mailer->send($message);
    }
}

<?php

/**
 * Форматирует цену
 *
 * @param number $price Цена
 *
 * @return string Отформатированная цена
 */
function formatPrice($price) {
    $ceiledPrice = ceil($price);
    $formattedPrice = number_format($ceiledPrice, 0, '.', ' ');

    return $formattedPrice;
}

/**
 * Добавляет валюту к цене
 *
 * @param string $price Отформатированная цена
 * @param string $className Название css класса для валюты
 * @param string $currency Название валюты
 *
 * @return string Возвращает html c валютой и ценой
 */
function addCurrencyToPrice($price, $className, $currency) {
    return "{$price}<b class={$className}>{$currency}</b>";
}

/**
 * Получает время до конца даты
 *
 * @param string $date Дата в виде строки
 *
 * @return array Возвращает время до окончания даты в формате массива, где первый элемент - часы, а второй - минуты
 */
function getDtRange($date) {
    // В одном дне 86400 секунд
    $endDate = strtotime($date);
    $secsToEnd = $endDate - time();

    $hours = str_pad(floor($secsToEnd / 3600), 2, "0", STR_PAD_LEFT);
    $minutes = str_pad(floor(($secsToEnd % 3600) / 60), 2, "0", STR_PAD_LEFT);

    return [$hours, $minutes];
}

/**
 * Получает время, которое прошло с момента даты
 *
 * @param string $endTime Дата в виде строки
 *
 * @return string Возвращает отформатированное время c прошедшей даты
 */
function getTimeLeft($endTime) {
    $timer = strtotime($endTime) - strtotime('now');

    if ($timer <=0) {
        return 0;
    }

    $days = floor($timer / 86400);
    $hours = floor($timer % 86400 / 3600);
    $minutes = floor($timer % 86400 % 3600 / 60);

    if ($days <= 0 && $hours <= 0) {
        return sprintf('%02d', $minutes);
    }

    if ($days <= 0) {
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    return sprintf('%02d:%02d:%02d', $days, $hours, $minutes);
}

/**
 * Получает информацию о ставке
 *
 * @param $bet Ставка
 * @param int $userId ID текущего пользователя
 *
 * @return string Возвращает информацию о ставке
 */
function getBetInfo($bet, $userId) {
    $endTime = $bet['date_close'];
    $endTimeResult = getTimeLeft($endTime);

    if ($bet['winner_id'] == $userId) {
        return "Ставка выиграла";
    }

    if ($endTimeResult <= 0) {
        return "Торги окончены";
    }

    return $endTimeResult;
}

/**
 * Получает ID текущего пользователя
 *
 * @return string Возвращает ID текущего польователя в виде строки
 */
function getUserId() {
    if (isset($_SESSION['user']['id'])) {
        return $_SESSION['user']['id'];
    }
}

/**
 * Получает текущую цену
 *
 * @param int $initialRate Начальная ставка
 * @param int $lastBet Последняя ставка
 *
 * @return int Возвращает текущую цену в виде числа
 */
function getCurrentPrice($initialRate, $lastBet) {
    if ($lastBet) {
        return $lastBet;
    }

    return $initialRate;
}

/**
 * Получает минимально возможную ставку
 *
 * @param int $initial_rate Начальная ставка
 * @param int $rate_step Шаг ставки
 * @param int $last_bet Последняя ставка
 *
 * @return int Возвращает минимально возможную ставку в виде числа
 */
function getMinimalBet($initial_rate, $rate_step, $last_bet) {
    if (!$last_bet) {
        return $initial_rate;
    }

    return $last_bet + $rate_step;
}

/**
 * Получает ID элемента
 *
 * @param $element Элемент
 *
 * @return int Возвращает ID элемента, если он существует
 */
function getId($element) {
    if ($element['id']) {
        return $element['id'];
    }
}

/**
 * Получает список категорий
 *
 * @param $link mysqli Ресурс соединения
 *
 * @return array Возвращает массив категорий, если они существует
 */
function getCategories($link) {
    $categoriesSql = 'SELECT id, name, symbol_code FROM category';
    $categoriesResult = mysqli_query($link, $categoriesSql);

    if (!$categoriesResult) {
        $error = mysqli_error($link);
        header("HTTP/1.0 404 Not Found");
    }

    $categories = mysqli_fetch_all($categoriesResult, MYSQLI_ASSOC);

    return $categories;
}

/**
 * Получает список ставок
 *
 * @param $link mysqli Ресурс соединения
 * @param string $lotId ID лота
 *
 * @return array Возвращает массив ставок, если они существуют
 */
function getBets($link, $lotId) {
    $betsSql = 'SELECT bet.rate as rate, bet.date_add as date_add, user.name as user FROM lot '
    . 'JOIN bet ON lot.id = bet.lot_id '
    . 'JOIN user on user.id = bet.user_id '
    . 'WHERE lot.id = ' . $lotId
    . ' ORDER BY lot.date_add DESC';
    $betsResult = mysqli_query($link, $betsSql);

    if (!$betsResult) {
        $error = mysqli_error($link);
        header("HTTP/1.0 404 Not Found");
    }

    $bets = mysqli_fetch_all($betsResult, MYSQLI_ASSOC);

    return $bets;
}

/**
 * Получает значение из формы
 *
 * @param string $name Название поля формы в виде строки
 *
 * @return Возвращает значение поля формы, если оно существует
 */
function getPostVal($name) {
    return $_POST[$name] ?? "";
}

/**
 * Валидирует категорию
 *
 * @param int $id ID категории в виде числа
 * @param array $allowedList Массив разрешенных категорий
 *
 * @return Возвращает ID категории, если оно валидно, если нет, то возращает null
 */
function validateCategory($id, $allowedList) {
    if (!in_array($id, $allowedList)) {
        return $id;
    }

    return null;
}

/**
 * Валидирует ставку
 *
 * @param int $rate Ставка в виде числа
 * @param int $min Минимальное значение ставки
 *
 * @return Возвращает текст ошибки валидации, если ставка невалидна, если нет, то возращает null
 */
function validateRate($rate, $min) {
    if ($rate < $min) {
        return "Значение должно быть больше $min";
    }

    if (!is_numeric($rate)) {
        return "Значение должно быть численным";
    }

    return null;
}

/**
 * Валидирует длину строки
 *
 * @param string $field Значение в виде строки
 * @param int $min Минимальная  длина значения
 * @param int $max Максимальная длина значения
 *
 * @return Возвращает текст ошибки валидации, если длина значения невалидна, если нет, то возращает null
 */
function validateLength($field, $min, $max) {
    $len = strlen($field);

    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }

    return null;
}

/**
 * Валидирует лот
 *
 * @param array $lot Лот
 * @param array $categoriesIds Массив ID категорий
 *
 * @return array Возвращает массив ошибок валидации лота
 */
function validateText($lot, $categoriesIds) {
    $errors = [];

    foreach ($lot as $key => $value) {
        switch ($key) {
            case 'category_id':
                $errors[$key] = validateCategory($value, $categoriesIds);
                break;
            case 'title':
                $errors[$key] = validateLength($value, 1, 255);
                break;
            case 'description':
                $errors[$key] = validateLength($value, 0, 255);
                break;
            case 'initial_rate':
                $errors[$key] = validateRate($value, 0);
                break;
            case 'rate_step':
                $errors[$key] = validateRate($value, 0);
                break;
            case 'date_close':
                $errors[$key] = validateLotDateClose($value);
                break;
        }
    }

    return array_filter($errors);
}

/**
 * Валидирует дату завершения лота
 *
 * @param string $date Дата завершения лота в виде строки
 *
 * @return Возвращает текст ошибки, если дата завершения лота невалидна, в противном случае возвращает null
 */
function validateLotDateClose($date) {
    if (!is_date_valid($date)) {
        return "Дата должна быть в формате ГГГГ-ММ-ДД";
    }

    if (strtotime($date) <= strtotime("tomorrow")) {
        return 'Дата закрытия лота должна быть больше текущей даты, хотя бы на один день.';
    }

    return null;
}

/**
 * Валидирует, что поля формы не пустые
 *
 * @param array $form Валидируемая форма
 * @param array $required Массив ключей обязательных полей
 *
 * @return array Возвращает массив ошибок валидации обязательных полей
 */
function validateRequiredFields($form, $required) {
    $errors = [];

    foreach ($required as $key) {
        if (empty($form[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    return array_filter($errors);
}

/**
 * Валидирует изображение лота
 *
 * @return array Возвращает массив ошибок валидации изображения лота
 */
function validateLotImage() {
    $errors = [];

    if (isset($_FILES['lot_image']) && $_FILES['lot_image']['tmp_name']) {
        $tmpName = $_FILES['lot_image']['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $tmpName);

        if ($fileType !== "image/jpeg" && $fileType !== 'image/png') {
            $errors['file'] = 'Загрузите картинку в формате JPEG или PNG';
        }
    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }

    return array_filter($errors);
}

/**
 * Валидирует форму добавления лота
 *
 * @param array $lot Лот
 * @param array $categoriesIds Массив ID категорий
 *
 * @return array Возвращает массив ошибок валидации добавляемого лота
 */
function validateLotForm($lot, $categoriesIds) {
    $required = ['title', 'category_id', 'description', 'initial_rate', 'rate_step', 'date_close'];

    $errorsText = validateText($lot, $categoriesIds);
    $errorsRequiredFields  = validateRequiredFields($lot, $required);
    $errorsImage = validateLotImage();
    $errorsForm = array_merge($errorsText, $errorsRequiredFields , $errorsImage);

    return $errorsForm;
}

/**
 * Обрабатывает загрузку картики
 *
 * @param $fileField Поле с картинкой
 *
 * @return string Возвращает адрес загруженной картинки в виде строки
 */
function handleImageUpload($fileField) {
    $tmpName = $fileField['tmp_name'];
    $filename = uniqid() . '.jpeg';
    $filepath = 'uploads/' . $filename;
    move_uploaded_file($tmpName, __DIR__ . '/uploads/' . $filename);

    return $filepath;
}

/**
 * Валидирует email
 *
 * @param string $email Email в виде строки
 *
 * @return array Возвращает массив ошибок валидации email
 */
function validateEmail($email) {
    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите валидный электронный адрес';
    }

    return array_filter($errors);
}

/**
 * Валидирует форму регистрации
 *
 * @param array $signupForm Форма регистрации в виде объекта
 *
 * @return array Возвращает массив ошибок валидации формы регистрации
 */
function validateSignupForm($signupForm) {
    $required = ['email', 'password', 'name', 'message'];

    $errorsEmail = validateEmail($signupForm['email']);
    $errorsRequiredFields  = validateRequiredFields($signupForm, $required);

    return array_merge($errorsEmail, $errorsRequiredFields );
}

/**
 * Валидирует форму логина
 *
 * @param array $signinForm Форма логина в виде объекта
 *
 * @return array Возвращает массив ошибок валидации формы логина
 */
function validateSigninForm($signinForm) {
    $required = ['email', 'password'];
    $errorsEmail = validateEmail($signinForm['email']);
    $errorsRequiredFields  = validateRequiredFields($signinForm, $required);

    return array_merge($errorsEmail, $errorsRequiredFields);
}

/**
 * Возвращает имя текущего пользователя
 *
 *
 * @return Возвращает имя пользователя в виде строки, если сеcсия активна, если нет, то null
 */
function getUsername() {
    return isset($_SESSION['user']) ? $_SESSION['user']['name'] : null;
}

/**
 * Проверяет авторизован пользователь или нет
 *
 * @return boolean Возвращает true, если пользователь авторизован, если нет, то false
 */
function isAuth() {
    return isset($_SESSION['user']);
}

/**
 * Валидирует форму добавления ставки
 *
 * @param array $bet Ставка
 * @param array $lot Лот
 *
 * @return array Возвращает массив ошибок валидации добавляемого ставки
 */
function validateBetForm($bet, $lot) {
    $errors = [];
    $minCost = $lot['current_rate'] + $lot['rate_step'];

    if (!is_int($bet)) {
        $errors['cost'] = 'Ставка должна быть целым числом';
    }

    if ($bet < $minCost) {
        $errors['cost'] = 'Ставка не может быть меньше '. $minCost .' ₽';
    }

    return array_filter($errors);
}

/**
 * Форматирует время, которое прошло в зависимости от числа единиц прошедшего времени
 *
 * @param string $time Время в виде строки
 * @param string $one Форма единственного числа
 * @param string $two Форма если единиц две
 * @param string $many Форма если единиц больше двух
 *
 * @return string Возвращает отформатированное прошедшее время с момента даты в виде строки
 */
function formatPassedTime($time, $one, $two, $many) {
    return sprintf('%s %s назад', $time, get_noun_plural_form($time, $one, $two, $many));
}


/**
 * Получает массив единиц времени для разного количества единиц
 *
 * @param string $timeUnit Единица времени
 *
 * @return string Возвращает массив единиц времени
 */
function getPluralNounArray($timeUnit) {
    switch ($timeUnit) {
        case 'секунда':
            return ['секунда', 'секунды', 'секунд'];
        case 'минута':
            return ['минута', 'минуты', 'минут'];
        case 'час':
            return ['час', 'часа', 'часов'];
        case 'день':
            return ['день', 'дня', 'дней'];
    }
}


/**
 * Получает время прошедшее с момента даты
 *
 * @param string $data_add Дата в виде строки
 * @param string $yearFormat Годичный формат времени
 *
 * @return string Возвращает отформатированное прошедшее время с момента даты в виде строки в зависимости от того, когда это дата наступила
 */
function getPassedTime($dateAdd, $timeFormat = 'H:i', $yearFormat = 'd.m.y в H:i') { // преобразовываем время в нормальный вид
    $date = new \DateTime($dateAdd);
    $today = new \DateTime('now', $date->getTimezone());
    $yesterday = new \DateTime('-1 day', $date->getTimezone());
    $minutesAgo = floor(($today->format('U') - $date->format('U')) / 60);
    $hoursAgo = floor(($today->format('U') - $date->format('U')) / 3660);

    if ($minutesAgo > 0) {
        switch (true) {
            case ($minutesAgo < 60):
                return formatPassedTime($minutesAgo, ...getPluralNounArray('минута'));
            case ($hoursAgo > 0 && $hoursAgo < 24 && $today->format('ymd') == $date->format('ymd')):
                return formatPassedTime($hoursAgo, ...getPluralNounArray('час'));
            case ($today->format('ymd') == $date->format('ymd')):
                return sprintf('Сегодня в %s', $date->format($timeFormat));
            case ($yesterday->format('ymd') == $date->format('ymd')):
                return sprintf('Вчера в %s', $date->format($timeFormat));
            default:
                return $date->format($yearFormat);
        }
    }

    return 'Меньше минуты назад';
}

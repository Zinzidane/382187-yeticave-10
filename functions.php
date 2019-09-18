<?php

/**
 * Форматирует цену
 *
 * @param number $price Цена
 *
 * @return string Отформатированная цена
 */
function formatPrice($price) {
    $ceiled_price = ceil($price);
    $formatted_price = number_format($ceiled_price, 0, '.', ' ');

    return $formatted_price;
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
    $end_date = strtotime($date);
    $secs_to_end = $end_date - time();

    $hours = str_pad(floor($secs_to_end / 3600), 2, "0", STR_PAD_LEFT);
    $minutes = str_pad(floor(($secs_to_end % 3600) / 60), 2, "0", STR_PAD_LEFT);

    return [$hours, $minutes];
}

/**
 * Получает время, которое прошло с момента даты
 *
 * @param string $end_time Дата в виде строки
 *
 * @return string Возвращает отформатированное время c прошедшей даты
 */
function getTimeLeft($end_time) {
    $timer = strtotime($end_time) - strtotime('now');

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
 * @param int $user_id ID текущего пользователя 
 *
 * @return string Возвращает информацию о ставке
 */
function getBetInfo($bet, $user_id) {
    $end_time = $bet['date_close'];
    $end_time_result = getTimeLeft($end_time);

    if ($bet['winner_id'] == $user_id) {
        return "Ставка выиграла";
    }

    if ($end_time_result <= 0) {
        return "Торги окончены";
    }

    return $end_time_result;
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
 * @param int $initial_rate Начальная ставка
 * @param int $last_bet Последняя ставка
 *
 * @return int Возвращает текущую цену в виде числа
 */
function getCurrentPrice($initial_rate, $last_bet) {
    if ($last_bet) {
        return $last_bet;
    }

    return $initial_rate;
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
    $categories_sql = 'SELECT id, name, symbol_code FROM category';
    $categories_result = mysqli_query($link, $categories_sql);

    if (!$categories_result) {
        $error = mysqli_error($link);
        header("HTTP/1.0 404 Not Found");
    }

    $categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);

    return $categories;
}

/**
 * Получает список ставок
 * 
 * @param $link mysqli Ресурс соединения
 * @param string $lot_id ID лота
 * 
 * @return array Возвращает массив ставок, если они существуют
 */
function getBets($link, $lot_id) {
    $bets_sql = 'SELECT bet.rate as rate, bet.date_add as date_add, user.name as user FROM lot '
    . 'JOIN bet ON lot.id = bet.lot_id '
    . 'JOIN user on user.id = bet.user_id '
    . 'WHERE lot.id = ' . $lot_id
    . ' ORDER BY lot.date_add DESC';
    $bets_result = mysqli_query($link, $bets_sql);

    if (!$bets_result) {
        $error = mysqli_error($link);
        header("HTTP/1.0 404 Not Found");
    }

    $bets = mysqli_fetch_all($bets_result, MYSQLI_ASSOC);

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
 * @param array $allowed_list Массив разрешенных категорий
 * 
 * @return Возвращает ID категории, если оно валидно, если нет, то возращает null
 */
function validateCategory($id, $allowed_list) {
    if (!in_array($id, $allowed_list)) {
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
 * @param array $categories_ids Массив ID категорий
 * 
 * @return array Возвращает массив ошибок валидации лота
 */
function validateText($lot, $categories_ids) {
    $errors = [];

    foreach ($lot as $key => $value) {
        switch ($key) {
            case 'category_id':
                $errors[$key] = validateCategory($value, $categories_ids);
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
        $tmp_name = $_FILES['lot_image']['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if ($file_type !== "image/jpeg" && $file_type !== 'image/png') {
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
 * @param array $categories_ids Массив ID категорий
 * 
 * @return array Возвращает массив ошибок валидации добавляемого лота
 */
function validateLotForm($lot, $categories_ids) {
    $required = ['title', 'category_id', 'description', 'initial_rate', 'rate_step', 'date_close'];

    $errors_text = validateText($lot, $categories_ids);
    $errors_required_fields = validateRequiredFields($lot, $required);
    $errors_image = validateLotImage();
    $errors_form = array_merge($errors_text, $errors_required_fields, $errors_image);

    return $errors_form;
}

/**
 * Обрабатывает загрузку картики
 * 
 * @param $file_field Поле с картинкой
 * 
 * @return string Возвращает адрес загруженной картинки в виде строки
 */
function handleImageUpload($file_field) {
    $tmp_name = $file_field['tmp_name'];
    $filename = uniqid() . '.jpeg';
    $filepath = 'uploads/' . $filename;
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    move_uploaded_file($tmp_name, __DIR__ . '/uploads/' . $filename);

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

    if (!filter_var($email, FILTER_validateEmail)) {
        $errors['email'] = 'Введите валидный электронный адрес';
    }

    return array_filter($errors);
}

/**
 * Валидирует форму регистрации
 * 
 * @param array $signup_form Форма регистрации в виде объекта
 * 
 * @return array Возвращает массив ошибок валидации формы регистрации
 */
function validateSignupForm($signup_form) {
    $required = ['email', 'password', 'name', 'message'];

    $errors_email = validateEmail($signup_form['email']);
    $errors_required_fields = validateRequiredFields($signup_form, $required);

    return array_merge($errors_email, $errors_required_fields);
}

/**
 * Валидирует форму логина
 * 
 * @param array $signin_form Форма логина в виде объекта
 * 
 * @return array Возвращает массив ошибок валидации формы логина
 */
function validateSigninForm($signin_form) {
    $required = ['email', 'password'];

    return validateRequiredFields($signin_form, $required);
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
    $min_cost = $lot['current_rate'] + $lot['rate_step'];

    if (!is_int($bet)) {
        $errors['cost'] = 'Ставка должна быть целым числом';
    }

    if ($bet < $min_cost) {
        $errors['cost'] = 'Ставка не может быть меньше '. $min_cost .' ₽';
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
 * @param string $time_unit Единица времени
 * 
 * @return string Возвращает массив единиц времени
 */
function getPluralNounArray($time_unit) {
    switch ($time_unit) {
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
 * @param string $month_format Месячный формат времени
 * @param string $year_format Годичный формат времени
 * 
 * @return string Возвращает отформатированное прошедшее время с момента даты в виде строки в зависимости от того, когда это дата наступила
 */
function getPassedTime($date_add, $time_format = 'H:i', $month_format = 'H:i d.m', $year_format = 'H:i d.m.Y') { // преобразовываем время в нормальный вид
    $date = new \DateTime($date_add);
    $today = new \DateTime('now', $date->getTimezone());
    $yesterday = new \DateTime('-1 day', $date->getTimezone());
    $minutes_ago = floor(($today->format('U') - $date->format('U')) / 60);
    $hours_ago = floor(($today->format('U') - $date->format('U')) / 3660);

    if ($minutes_ago > 0) {
        if ($minutes_ago < 60) {
            return formatPassedTime($minutes_ago, ...getPluralNounArray('минута'));
        } elseif ($hours_ago > 0 && $hours_ago < 24 && $today->format('ymd') == $date->format('ymd')) {
            return formatPassedTime($hours_ago, ...getPluralNounArray('час'));
        } elseif ($today->format('ymd') == $date->format('ymd')) {
            return sprintf('Сегодня в %s', $date->format($time_format));
        } elseif ($yesterday->format('ymd') == $date->format('ymd')) {
            return sprintf('Вчера в %s', $date->format($time_format));
        } elseif ($today->format('Y') == $date->format('Y')) {
            return $date->format($month_format);
        } else {
            return $date->format($year_format);
        }
    }

    return 'Меньше минуты назад';
}

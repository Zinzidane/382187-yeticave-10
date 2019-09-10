<?php
function format_price($price) {
    $ceiled_price = ceil($price);
    $formatted_price = number_format($ceiled_price, 0, '.', ' ');

    return $formatted_price;
}

function add_currency_to_price($price, $className, $currency) {
    return "{$price}<b class={$className}>{$currency}</b>";
}

function get_dt_range($date) {
    // В одном дне 86400 секунд
    $end_date = strtotime($date);
    $secs_to_end = $end_date - time();

    $hours = str_pad(floor($secs_to_end / 3600), 2, "0", STR_PAD_LEFT);
    $minutes = str_pad(floor(($secs_to_end % 3600) / 60), 2, "0", STR_PAD_LEFT);

    return [$hours, $minutes];
}

function get_time_left($end_time) {
    $timer = strtotime($end_time) - strtotime('now');

    if ($timer <=0 ) {
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

function get_bet_info($bet, $user_id) {
    $end_time = $bet['date_close'];
    $end_time_result = get_time_left($end_time);

    if ($bet['winner_id'] == $user_id) {
        return "Ставка выиграла";
    }

    if ($end_time_result <= 0) {
        return "Торги окончены";
    }

    return $end_time_result;
}

function get_user_id() {
    return $_SESSION['user']['id'];
}

function get_current_price($initial_rate, $last_bet) {
    if ($last_bet) {
        return $last_bet;
    }

    return $initial_rate;
}

function get_minimal_bet($initial_rate, $rate_step, $last_bet) {
    if (!$last_bet) {
        return $initial_rate;
    }

    return $last_bet + $rate_step;
}

function get_id($element) {
    if ($element['id']) {
        return $element['id'];
    }
}

function get_post_val($name) {
    return $_POST[$name] ?? "";
}

function validate_category($id, $allowed_list) {
    if (!in_array($id, $allowed_list)) {
        return $id;
    }

    return null;
}

function validate_rate($rate, $min) {
    if ($rate < $min) {
        return "Значение должно быть больше $min";
    }

    if (!is_numeric($rate)) {
        return "Значение должно быть численным";
    }

    return null;
}

function validate_length($field, $min, $max) {
    $len = strlen($field);

    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }

    return null;
}

function validate_text($lot, $categories_ids) {
    $errors = [];

    foreach ($lot as $key => $value) {
        switch ($key) {
            case 'category_id':
                $errors[$key] = validate_category($value, $categories_ids);
                break;
            case 'title':
                $errors[$key] = validate_length($value, 1, 255);
                break;
            case 'description':
                $errors[$key] = validate_length($value, 0, 255);
                break;
            case 'initial_rate':
                $errors[$key] = validate_rate($value, 0);
                break;
            case 'rate_step':
                $errors[$key] = validate_rate($value, 0);
                break;
            case 'date_close':
                $errors[$key] = is_date_valid($key);
                break;
        }
    }

    return array_filter($errors);
}

function validate_required_fields($form, $required) {
    $errors = [];

    foreach ($required as $key) {
        if (empty($form[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    return array_filter($errors);
}

function validate_image($lot) {
    $errors = [];

    if (isset($_FILES['lot_image']['name'])) {
        $tmp_name = $_FILES['lot_image']['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if ($file_type !== "image/jpeg") {
            $errors['file'] = 'Загрузите картинку в формате JPEG';
        }
    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }

    return array_filter($errors);
}

function validate_lot_form($lot, $categories_ids) {
    $required = ['title', 'category_id', 'description', 'initial_rate', 'rate_step', 'date_close'];

    $errors_text = validate_text($lot, $categories_ids);
    $errors_required_fields = validate_required_fields($lot, $required);
    $errors_image = validate_image($lot);
    $errors_form = array_merge($errors_text, $errors_required_fields, $errors_image);

    return $errors_form;
}

function handle_image_upload($file_field) {
    $tmp_name = $file_field['tmp_name'];
    $path = $file_field['name'];
    $filename = uniqid() . '.jpeg';
    $filepath = 'uploads/' . $filename;
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $tmp_name);
    move_uploaded_file($tmp_name, __DIR__ . '/uploads/' . $filename);

    return $filepath;
}

function validate_email($email) {
    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите валидный электронный адрес';
    }

    return array_filter($errors);
}

function validate_signup_form($signup_form) {
    $required = ['email', 'password', 'name', 'message'];

    $errors_email = validate_email($signup_form['email']);
    $errors_required_fields = validate_required_fields($signup_form, $required);

    return array_merge($errors_email, $errors_required_fields);
}

function validate_signin_form($signin_form) {
    $required = ['email', 'password'];

    return validate_required_fields($signin_form, $required);
}

function get_username() {
    return isset($_SESSION['user']) ? $_SESSION['user']['name'] : null;
}

function is_auth() {
    return isset($_SESSION['user']);
}

function validate_bet_form($bet, $lot) {
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

function format_passed_time($time, $one, $two, $many) {
    return sprintf('%s %s назад', $time, get_noun_plural_form($time, $one, $two, $many));
}

function get_plural_noun_array($time_unit) {
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

function get_passed_time($date_add, $time_format = 'H:i', $month_format = 'H:i d.m', $year_format = 'H:i d.m.Y') { // преобразовываем время в нормальный вид
    $date = new \DateTime($date_add);
    $today = new \DateTime('now', $date->getTimezone());
    $yesterday = new \DateTime('-1 day', $date->getTimezone());
    $tomorrow = new \DateTime('+1 day', $date->getTimezone());
    $minutes_ago = floor(($today->format('U') - $date->format('U')) / 60);
    $hours_ago = floor(($today->format('U') - $date->format('U')) / 3660);

    if ($minutes_ago > 0) {
        if ($minutes_ago < 60) {
            return format_passed_time($minutes_ago, ...get_plural_noun_array('минута'));
        } elseif ($hours_ago > 0 && $hours_ago < 24 && $today->format('ymd') == $date->format('ymd')) {
            return format_passed_time($hours_ago, ...get_plural_noun_array('час'));
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

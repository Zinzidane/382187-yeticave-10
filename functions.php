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
    $ts_midnight = strtotime($date);
    $secs_to_midnight = $ts_midnight - time();

    $hours = str_pad(floor($secs_to_midnight / 3600), 2, "0", STR_PAD_LEFT);
    $minutes = str_pad(floor(($secs_to_midnight % 3600) / 60), 2, "0", STR_PAD_LEFT);

    return [$hours, $minutes];
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

function getPostVal($name) {
    return $_POST[$name] ?? "";
}

function validateCategory($name, $allowed_list) {
    $id = $_POST[$name];

    if (!in_array($id, $allowed_list)) {
        // return "Указана несуществующая категория";
        return $id;
    }

    return null;
}

function validateRate($name, $min) {
    $rate = $_POST[$name];

    if ($rate < $min) {
        return "Значение должно быть больше $min";
    }

    if (!is_numeric($rate)) {
        return "Значение должно быть численным";
    }

    return null;
}

function validateLength($name, $min, $max) {
    $len = strlen($_POST[$name]);

    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }

    return null;
}

function validate_form($lot, $categories_ids) {
    $required = ['title', 'category_id', 'description', 'initial_rate', 'rate_step', 'date_close'];
    $errors = [];

    foreach ($lot as $key => $value) {
        switch ($key) {
            case 'category_id':
                $errors[$key] = validateCategory('category_id', $categories_ids);
                break;
            case 'title':
                $errors[$key] = validateLength('title', 1, 255);
                break;
            case 'description':
                $errors[$key] = validateLength('description', 0, 255);
                break;
            case 'initial_rate':
                $errors[$key] = validateRate('initial_rate', 0);
                break;
            case 'rate_step':
                $errors[$key] = validateRate('rate', 0);
                break;
            case 'date_close':
                $errors[$key] = is_date_valid('date_close');
                break;
        }
     }

    foreach ($required as $key) {
        if (empty($lot[$key])) {
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
        } else {
            move_uploaded_file($tmp_name, __DIR__ . '/uploads/' . $filename);
            $lot['image'] = $filename;
        }
    } else {
        $errors['file'] = 'Вы не загрузили файл';
    }

    return $errors;
}

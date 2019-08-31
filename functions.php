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

function validate_category($id, $allowed_list) {
    if (!in_array($id, $allowed_list)) {
        // return "Указана несуществующая категория";
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
                if (empty($lot[$key])) {
                    $errors[$key] = 'Это поле надо заполнить';
                }
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

function validate_required_fields($lot, $required) {
    $errors = [];

    foreach ($required as $key) {
        if (empty($lot[$key])) {
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

function validate_form($lot, $categories_ids) {
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
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $tmp_name);
    move_uploaded_file($tmp_name, __DIR__ . '/uploads/' . $filename);

    return $filename;
}

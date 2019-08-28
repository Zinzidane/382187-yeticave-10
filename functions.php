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

    return null;
}

function validateLength($name, $min, $max) {
    $len = strlen($_POST[$name]);

    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }

    return null;
}

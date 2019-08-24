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

function calculate_current_price($initial_rate, $last_bet) {
    if ($last_bet) {
        return $last_bet;
    }

    return $initial_rate;
}

function calculate_minimal_bet($initial_rate, $rate_step, $last_bet) {
    if (!$last_bet) {
        return $initial_rate;
    }

    return $last_bet + $rate_step;
}

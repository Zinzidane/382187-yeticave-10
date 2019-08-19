<?php
require_once('helpers.php');

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

$is_auth = rand(0, 1);
$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];
$lots = [
    [
        "name" => '2014 Rossignol District Snowboard',
        "category" => 'Доски и лыжи',
        "price" => 10999,
        'img' => 'img/lot-1.jpg',
        'date_end' => '2019-08-19'
    ],
    [
        "name" => 'DC Ply Mens 2016/2017 Snowboard',
        "category" => 'Доски и лыжи',
        "price" => 159999,
        'img' => 'img/lot-2.jpg',
        'date_end' => '2019-08-22'
    ],
    [
        "name" => 'Крепления Union Contact Pro 2015 года размер L/XL',
        "category" => 'Крепления',
        "price" => 8000,
        'img' => 'img/lot-3.jpg',
        'date_end' => '2019-08-25'
    ],
    [
        "name" => 'Ботинки для сноуборда DC Mutiny Charocal',
        "category" => 'Ботинки',
        "price" => 10999,
        'img' => 'img/lot-4.jpg',
        'date_end' => '2019-08-22'
    ],
    [
        "name" => 'Куртка для сноуборда DC Mutiny Charocal',
        "category" => 'Одежда',
        "price" => 7500,
        'img' => 'img/lot-5.jpg',
        'date_end' => '2019-08-23'
    ],
    [
        "name" => 'Маска Oakley Canopy',
        "category" => 'Разное',
        "price" => 5400,
        'img' => 'img/lot-6.jpg',
        'date_end' => '2019-08-19'
    ]
];
$page_content = include_template('main.php', ['categories' => $categories, 'lots' => $lots]);
$layout_content = include_template('layout.php', [
    'title' => 'Главная',
    'username' => 'Ваня',
    'is_auth' => $is_auth,
    'content' => $page_content,
    'categories' => $categories
]);

print($layout_content);
?>

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

$is_auth = rand(0, 1);
$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];
$lots = [
    [
        "name" => '2014 Rossignol District Snowboard',
        "category" => 'Доски и лыжи',
        "price" => 10999,
        'img' => 'img/lot-1.jpg'
    ],
    [
        "name" => 'DC Ply Mens 2016/2017 Snowboard',
        "category" => 'Доски и лыжи',
        "price" => 159999,
        'img' => 'img/lot-2.jpg'
    ],
    [
        "name" => 'Крепления Union Contact Pro 2015 года размер L/XL',
        "category" => 'Крепления',
        "price" => 8000,
        'img' => 'img/lot-3.jpg'
    ],
    [
        "name" => 'Ботинки для сноуборда DC Mutiny Charocal',
        "category" => 'Ботинки',
        "price" => 10999,
        'img' => 'img/lot-4.jpg'
    ],
    [
        "name" => 'Куртка для сноуборда DC Mutiny Charocal',
        "category" => 'Одежда',
        "price" => 7500,
        'img' => 'img/lot-5.jpg'
    ],
    [
        "name" => 'Маска Oakley Canopy',
        "category" => 'Разное',
        "price" => 5400,
        'img' => 'img/lot-6.jpg'
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

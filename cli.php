<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';

use Melni\AdvancedCoursePhp\Blog\{User, Post, Comment};

$faker = Faker\Factory::create('ru_RU');

$message = 'Введите один из аргументов:' . PHP_EOL . 'user' . PHP_EOL . 'comment' . PHP_EOL . 'post';

if (empty($argv[1])) {
    die($message);
} else {
    $inputData = $argv[1];
}

switch ($inputData) {
    case 'user':
        echo createUser($faker);
        break;
    case 'post':
        echo createPost($faker);
        break;
    case 'comment':
        echo createComment($faker);
        break;
    default:
        echo $message;
}

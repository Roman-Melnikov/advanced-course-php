<?php

use Melni\AdvancedCoursePhp\Blog\{UUID};
use Melni\AdvancedCoursePhp\Exceptions\AppException;
use Melni\AdvancedCoursePhp\Blog\Commands\CreateUserCommand;
use Melni\AdvancedCoursePhp\Blog\Commands\Arguments;

$faker = Faker\Factory::create('ru_RU');

$container = require __DIR__ . '/bootstrap.php';

try {
    $command = $container->get(CreateUserCommand::class);
    $command->hanle(Arguments::fromArgv($argv));

} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}

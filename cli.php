<?php

use Melni\AdvancedCoursePhp\Exceptions\AppException;
use Melni\AdvancedCoursePhp\Blog\Commands\CreateUserCommand;
use Melni\AdvancedCoursePhp\Blog\Commands\Arguments;
use Monolog\Logger;

//$faker = Faker\Factory::create('ru_RU');

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(Logger::class);

try {
    $command = $container->get(CreateUserCommand::class);
    $command->hanle(Arguments::fromArgv($argv));

} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
}

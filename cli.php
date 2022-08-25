<?php

use Melni\AdvancedCoursePhp\Exceptions\AppException;
use Melni\AdvancedCoursePhp\Blog\Commands\CreateUserCommand;
use Melni\AdvancedCoursePhp\Blog\Commands\Arguments;
use Psr\Log\LoggerInterface;

//$faker = Faker\Factory::create('ru_RU');

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

try {
    $command = $container->get(CreateUserCommand::class);
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
}

<?php

use Melni\AdvancedCoursePhp\Blog\Commands\Arguments;
use Melni\AdvancedCoursePhp\Blog\Commands\CreateUserCommand;
use Melni\AdvancedCoursePhp\Blog\{Comment,
    Post,
    Repositories\SqliteCommentsRepository,
    Repositories\SqlitePostsRepository,
    UUID
};
use Melni\AdvancedCoursePhp\Blog\Repositories\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');

$pdo = require 'db.php';

$usersRepository = new SqliteUsersRepository($pdo);

$command = new CreateUserCommand($usersRepository);

try {
    $command->handle(Arguments::fromArgv($argv));

    $user = $usersRepository->getByUsername(Arguments::fromArgv($argv)->get('username'));
    $post = new Post(UUID::random(), $user, $faker->realText(rand(20, 40)), $faker->realText(rand(50, 100)));
    $comment = new Comment(UUID::random(), $user, $post, $faker->realText(rand(50, 100)));

    $postsRepository = new SqlitePostsRepository($pdo);
    $postsRepository->save($post);

    $commentsRepository = new SqliteCommentsRepository($pdo);
    $commentsRepository->save($comment);
} catch (Exception $exception) {
    echo $exception->getMessage();
}

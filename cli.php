<?php

use Melni\AdvancedCoursePhp\Blog\{Comment, Post, UUID};
use Melni\AdvancedCoursePhp\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Melni\AdvancedCoursePhp\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Melni\AdvancedCoursePhp\Blog\Commands\Arguments;
use Melni\AdvancedCoursePhp\Blog\Commands\CreateUserCommand;
use Melni\AdvancedCoursePhp\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');

$pdo = require 'db.php';
try {
    $usersRepository = new SqliteUsersRepository($pdo);

    $command = new CreateUserCommand($usersRepository);


    $command->handle(Arguments::fromArgv($argv));

    $user = $usersRepository->getByUsername(
        Arguments::fromArgv($argv)->get('username')
    );

    $post = new Post(
        UUID::random(),
        $user,
        $faker->realText(rand(20, 30)),
        $faker->realText(rand(200, 280)));

    $comment = new Comment(
        UUID::random(),
        $user, $post,
        $faker->realText(rand(100, 200)));

    $postsRepository = new SqlitePostsRepository($pdo);
    $postsRepository->save($post);

    $commentsRepository = new SqliteCommentsRepository($pdo);
    $commentsRepository->save($comment);

    $postByUuid = $postsRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));
    var_dump($postByUuid);
} catch (Exception $exception) {
    echo $exception->getMessage();
}

<?php

use Melni\AdvancedCoursePhp\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Melni\AdvancedCoursePhp\Blog\{Repositories\UsersRepository\SqliteUsersRepository, UUID};
use Melni\AdvancedCoursePhp\AppException;
use Melni\AdvancedCoursePhp\Blog\Repositories\PostsRepository\SqlitePostsRepository;

require_once __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');

$pdo = require 'db.php';
try {
    $usersRepository = new SqliteUsersRepository($pdo);
    $user = $usersRepository->get(new UUID('104e8613-b7b2-4cb9-8296-56a765033ff9'));
//    print_r($user);//
//
//    $command = new CreateUserCommand($usersRepository);
//
//
//    $command->handle(Arguments::fromArgv($argv));
//
//    $user = $usersRepository->getByUsername(
//        Arguments::fromArgv($argv)->get('username')
//    );
//
//    $post = new Post(
//        UUID::random(),
//        $user,
//        $faker->realText(rand(20, 30)),
//        $faker->realText(rand(200, 280)));
//
//    $comment = new Comment(
//        UUID::random(),
//        $user, $post,
//        $faker->realText(rand(100, 200)));

//    $postsRepository = new SqlitePostsRepository($pdo);
//    $postsRepository->save($post);

//    $commentsRepository = new SqliteCommentsRepository($pdo);
//    $commentsRepository->save($comment);

//    $post = $postsRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));
//    print_r($post);

//    $commentsRepository = new SqliteCommentsRepository($pdo);
//    $comment = $commentsRepository->get(new UUID('996a1126-7ef8-4b88-ac9b-32511c3a0384'));
//    print_r($comment);

} catch (AppException $exception) {
    echo $exception->getMessage();
}

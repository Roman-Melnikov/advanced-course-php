<?php

use Melni\AdvancedCoursePhp\Container\DIContainer;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\UsersRepository\SqliteUsersRepository;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\PostsRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\PostsRepository\SqlitePostsRepository;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\CommentsRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\CommentsRepository\SqliteCommentsRepository;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\PostsLikesRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\LikesRepository\SqlitePostsLikesRepository;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\CommentsLikesRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\LikesRepository\SqliteCommentsLikesRepository;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Melni\AdvancedCoursePhp\Http\Auth\IdentificationInterface;
use Melni\AdvancedCoursePhp\Http\Auth\JsonBodyUsernameIdentification;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$logger = (new Logger('blog'));

if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger
        ->pushHandler(
            new StreamHandler(
                __DIR__ . '/logs/blog.log'
            )
        )
        ->pushHandler(
            new StreamHandler(
                __DIR__ . '/logs/blog.error.log',
                level: Logger::ERROR,
                bubble: false
            )
        );
}

if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
        ->pushHandler(
            new StreamHandler('php://stdout')
        );
}

$container = new DIContainer();

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
    PostsLikesRepositoryInterface::class,
    SqlitePostsLikesRepository::class
);

$container->bind(
    CommentsLikesRepositoryInterface::class,
    SqliteCommentsLikesRepository::class
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUsernameIdentification::class
);

$container->bind(
    \PDO::class,
    new \PDO(
        $_SERVER['SQLITE_DB_PATH'],
        null,
        null,
        [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    )
);

$container->bind(
    LoggerInterface::class,
    $logger
);

return $container;
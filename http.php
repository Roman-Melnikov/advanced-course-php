<?php

use Melni\AdvancedCoursePhp\Http\Actions\Users\FindByUsername;
use Melni\AdvancedCoursePhp\Repositories\UsersRepository\SqliteUsersRepository;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\FindByUuidPost;
use Melni\AdvancedCoursePhp\Repositories\PostsRepository\SqlitePostsRepository;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\CreatePost;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Actions\Comments\CreateComment;
use Melni\AdvancedCoursePhp\Repositories\CommentsRepository\SqliteCommentsRepository;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\RemovePost;
use Melni\AdvancedCoursePhp\Http\Actions\Comments\FindByUuidComment;
use Melni\AdvancedCoursePhp\Http\Actions\Comments\RemoveComment;

require_once __DIR__ . '/vendor/autoload.php';

$pdo = require 'db.php';

$routes = [
    'GET' => [
        '/users/show' => new FindByUsername(
            new SqliteUsersRepository($pdo)
        ),
        '/posts/show' => new FindByUuidPost(
            new SqlitePostsRepository($pdo)
        ),
        '/comments/show' => new FindByUuidComment(
            new SqliteCommentsRepository($pdo)
        )
    ],
    'POST' => [
        '/posts/create' => new CreatePost(
            new SqlitePostsRepository($pdo),
            new SqliteUsersRepository($pdo)
        ),
        '/comments/create' => new CreateComment(
            new SqliteCommentsRepository($pdo),
            new SqlitePostsRepository($pdo),
            new SqliteUsersRepository($pdo)
        ),
    ],
    'DELETE' => [
        '/posts' => new RemovePost(
            new SqlitePostsRepository($pdo)
        ),
        '/comments' => new RemoveComment(
            new SqliteCommentsRepository($pdo)
        ),
    ],
];

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

try {
    $method = $request->method();
    $path = $request->path();
} catch (HttpException $e) {
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("$method not found"))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("$path not found"))->send();
    return;
}

$action = $routes[$method][$path];

$response = $action->handle($request);

$response->send();


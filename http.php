<?php

use Melni\AdvancedCoursePhp\Http\Actions\Users\FindByUsername;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\FindByUuidPost;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\CreatePost;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Actions\Comments\CreateComment;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\RemovePost;
use Melni\AdvancedCoursePhp\Http\Actions\Comments\FindByUuidComment;
use Melni\AdvancedCoursePhp\Http\Actions\Comments\RemoveComment;
use Melni\AdvancedCoursePhp\Http\Actions\Likes\CreateLike;
use Melni\AdvancedCoursePhp\Http\Actions\Likes\RemoveLike;

$container = require __DIR__ . '/bootstrap.php';

$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindByUuidPost::class,
        '/comments/show' => FindByUuidComment::class,
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/comments/create' => CreateComment::class,
        '/likes/create' => CreateLike::class,
    ],
    'DELETE' => [
        '/posts' => RemovePost::class,
        '/comments' => RemoveComment::class,
        '/likes' => RemoveLike::class,
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

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

$response = $action->handle($request);

$response->send();


<?php

use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Http\Actions\Auth\LogIn;
use Melni\AdvancedCoursePhp\Http\Actions\Auth\LogOut;
use Melni\AdvancedCoursePhp\Http\Actions\Comments\CreateComment;
use Melni\AdvancedCoursePhp\Http\Actions\Comments\FindByUuidComment;
use Melni\AdvancedCoursePhp\Http\Actions\Comments\RemoveComment;
use Melni\AdvancedCoursePhp\Http\Actions\Likes\CreateCommentLike;
use Melni\AdvancedCoursePhp\Http\Actions\Likes\CreatePostLike;
use Melni\AdvancedCoursePhp\Http\Actions\Likes\FindByUuidCommentLikes;
use Melni\AdvancedCoursePhp\Http\Actions\Likes\FindByUuidPostLikes;
use Melni\AdvancedCoursePhp\Http\Actions\Likes\RemoveCommentLike;
use Melni\AdvancedCoursePhp\Http\Actions\Likes\RemovePostLike;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\CreatePost;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\FindByUuidPost;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\RemovePost;
use Melni\AdvancedCoursePhp\Http\Actions\Users\CreateUser;
use Melni\AdvancedCoursePhp\Http\Actions\Users\FindByUsername;
use Melni\AdvancedCoursePhp\Http\Actions\Users\RemoveUser;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindByUuidPost::class,
        '/comments/show' => FindByUuidComment::class,
        '/postsLikes/show' => FindByUuidPostLikes::class,
        '/commentsLikes/show' => FindByUuidCommentLikes::class,
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/comments/create' => CreateComment::class,
        '/postLikes/create' => CreatePostLike::class,
        '/commentLikes/create' => CreateCommentLike::class,
        '/users/create' => CreateUser::class,
        '/login' => LogIn::class,
        '/logout' => LogOut::class,
    ],
    'DELETE' => [
        '/users' => RemoveUser::class,
        '/posts' => RemovePost::class,
        '/comments' => RemoveComment::class,
        '/postLikes' => RemovePostLike::class,
        '/commentLikes' => RemoveCommentLike::class,
    ],
];

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

$logger = $container->get(LoggerInterface::class);

try {
    $method = $request->method();
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

if (!array_key_exists($method, $routes) ||
    !array_key_exists($path, $routes[$method])) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$actionClassName = $routes[$method][$path];

try {
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse)->send();
    return;
}

$response->send();
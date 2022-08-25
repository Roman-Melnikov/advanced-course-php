<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Posts;

use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AuthException;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\Auth\TokenAuthenticationInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\PostsRepositoryInterface;

class CreatePost implements ActionsInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface  $authentication,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newPostUuid = UUID::random();

        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->JsonBodyField('title'),
                $request->JsonBodyField('text')
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->save($post);

        return new SuccessFulResponse(
            ['uuid' => (string)$newPostUuid]
        );
    }
}
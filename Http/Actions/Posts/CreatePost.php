<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Posts;

use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\Repositories\Interfaces\PostsRepositoryInterface;
use Melni\AdvancedCoursePhp\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\HttpException;
use Melni\AdvancedCoursePhp\InvalidUuidException;
use Melni\AdvancedCoursePhp\UserNotFoundException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;

class CreatePost implements ActionsInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    /**
     * @throws InvalidUuidException
     */
    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->JsonBodyField('author_uuid'));
        } catch (HttpException|InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
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
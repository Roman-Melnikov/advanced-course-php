<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Comments;

use Melni\AdvancedCoursePhp\Blog\Comment;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AppException;
use Melni\AdvancedCoursePhp\Exceptions\AuthException;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\Auth\IdentificationInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\CommentsRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\PostsRepositoryInterface;

class CreateComment implements ActionsInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface    $postsRepository,
        private IdentificationInterface $identification
    )
    {
    }

    /**
     * @throws InvalidUuidException
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->identification->user($request);
            $postUuid = $request->JsonBodyField('post_uuid');
            $txt = $request->JsonBodyField('text');
        } catch (HttpException|AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->get(new UUID($postUuid));
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newUuidComment = UUID::random();

        $comment = new Comment(
            $newUuidComment,
            $user,
            $post,
            $txt
        );

        $this->commentsRepository->save($comment);

        return new SuccessFulResponse(
            ['data' => (string)$newUuidComment]
        );
    }
}
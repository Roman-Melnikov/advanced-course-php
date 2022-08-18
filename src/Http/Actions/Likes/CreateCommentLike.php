<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Likes;

use Melni\AdvancedCoursePhp\Blog\Likes\CommentLike;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AppException;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Exceptions\LikeAlreadyExists;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\CommentsLikesRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\CommentsRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;

class CreateCommentLike implements ActionsInterface
{
    public function __construct(
        private CommentsLikesRepositoryInterface $likesRepository,
        private CommentsRepositoryInterface      $commentsRepository,
        private UsersRepositoryInterface      $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $commentUuid = $request->JsonBodyField('comment_uuid');
            $userUuid = $request->JsonBodyField('user_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->likesRepository->checkUserLikeForCommentExists($commentUuid, $userUuid);
        } catch (LikeAlreadyExists $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $newLikeUuid = UUID::random();
            $comment = $this->commentsRepository->get(new UUID($commentUuid));
            $user = $this->usersRepository->get(new UUID($userUuid));
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $like = new CommentLike(
            $newLikeUuid,
            $user,
            $comment
        );

        $this->likesRepository->save($like);

        return new SuccessFulResponse(
            ['uuid' => (string)$newLikeUuid]
        );
    }
}
<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Comments;

use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AppException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\CommentsRepositoryInterface;

class FindByUuidComment implements ActionsInterface
{

    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = $request->JsonBodyField('comment_uuid');
            $comment = $this->commentsRepository->get(new UUID($uuid));
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            [
                'post_uuid' => (string)($comment->getPost())->getUuid(),
                'txt' => $comment->getText(),
                'author' => (string)$comment->getUser()
            ]
        );
    }
}
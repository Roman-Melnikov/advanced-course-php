<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Likes;

use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AppException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\CommentsLikesRepositoryInterface;

class RemoveCommentLike implements ActionsInterface
{
    public function __construct(
        private CommentsLikesRepositoryInterface $likesRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
            $this->likesRepository->remove($uuid);
        }catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            ['action' => 'likes ' . $uuid . ' deleted successfully']
        );
    }
}
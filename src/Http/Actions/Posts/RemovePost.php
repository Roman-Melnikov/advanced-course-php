<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Posts;

use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AppException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\PostsRepositoryInterface;

class RemovePost implements ActionsInterface
{

    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
            $this->postsRepository->remove($uuid);
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            ['action' => 'post ' . $uuid . ' deleted successfully']
        );
    }
}
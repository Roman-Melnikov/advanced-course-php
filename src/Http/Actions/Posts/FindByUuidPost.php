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

class FindByUuidPost implements ActionsInterface
{

    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = $request->JsonBodyField('post_uuid');
            $post = $this->postsRepository->get(new UUID($uuid));
        } catch (AppException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            [
                'title' => $post->getTitle(),
                'text' => $post->getText(),
                'author' => (string)$post->getAutor()
            ]
        );
    }
}
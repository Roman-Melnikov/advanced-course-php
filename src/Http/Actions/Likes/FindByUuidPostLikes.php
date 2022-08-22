<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Likes;

use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Exceptions\LikeNotFoundException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\PostsLikesRepositoryInterface;

class FindByUuidPostLikes implements ActionsInterface
{

    public function __construct(
        private PostsLikesRepositoryInterface $repository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException|InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $likes = $this->repository->getByPostUuid($uuid);
        } catch (LikeNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $outputMas = [];

        foreach ($likes as $like) {
            $outputMas[] = [
                'uuid' => $like['uuid'],
                'user_uuid' => $like['user_uuid']
            ];
        }

        return new SuccessFulResponse(
            ['post_likes' => $outputMas]
        );
    }
}
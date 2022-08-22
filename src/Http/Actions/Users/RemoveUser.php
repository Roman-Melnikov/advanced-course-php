<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Users;

use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class RemoveUser implements ActionsInterface
{

    public function __construct(
        private UsersRepositoryInterface $repository,
        private LoggerInterface          $logger
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('user_uuid'));
        } catch (HttpException|InvalidUuidException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        $this->repository->remove($uuid);

        return new SuccessFulResponse(
            ['action' => 'user ' . $uuid . ' deleted successfully']
        );
    }
}
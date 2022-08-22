<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Users;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Person\Name;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateUser implements ActionsInterface
{

    public function __construct(
        private UsersRepositoryInterface $repository,
        private LoggerInterface          $logger
    )
    {
    }

    /**
     * @throws InvalidUuidException
     * @throws HttpException
     */
    public function handle(Request $request): Response
    {
        $username = $request->JsonBodyField('username');

        if ($this->userExists($username)) {
            $message = "This $username already exists";

            $this->logger->warning($message);
            return new ErrorResponse($message);
        }

        $first = $request->JsonBodyField('first_name');
        $last = $request->JsonBodyField('last_name');

        $uuid = UUID::random();

        $user = new User(
            $uuid,
            new Name($first, $last),
            $username
        );

        $this->repository->save($user);

        return new SuccessFulResponse(
            ['username' => $username]
        );

    }

    private function userExists(string $username): bool
    {
        try {
            $this->repository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
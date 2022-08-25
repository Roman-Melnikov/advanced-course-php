<?php

namespace Melni\AdvancedCoursePhp\Http\Auth;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AuthException;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;

class JsonBodyUuidAuthentication implements AuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $repository
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $uuid = new UUID($request->JsonBodyField('user_uuid'));
        } catch (HttpException|InvalidUuidException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            return $this->repository->get($uuid);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}
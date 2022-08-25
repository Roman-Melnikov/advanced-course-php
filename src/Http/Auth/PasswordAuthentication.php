<?php

namespace Melni\AdvancedCoursePhp\Http\Auth;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Exceptions\AuthException;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;

class PasswordAuthentication implements PasswordAuthenticationInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $username = $request->JsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $password = $request->JsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)) {
            throw new AuthException(
                'Wrong password'
            );
        }

        return $user;
    }
}
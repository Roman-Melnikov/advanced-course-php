<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Users;

use Melni\AdvancedCoursePhp\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use Melni\AdvancedCoursePhp\HttpException;
use Melni\AdvancedCoursePhp\UserNotFoundException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;

class FindByUsername implements ActionsInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $username = $request->query('username');
            $user = $this->usersRepository->getByUsername($username);
        } catch (HttpException|UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessFulResponse(
            [
                'username' => $user->getUsername(),
                'name' => (string)$user->getName()
            ]
        );
    }
}
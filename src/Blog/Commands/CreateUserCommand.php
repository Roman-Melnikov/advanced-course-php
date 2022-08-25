<?php

namespace Melni\AdvancedCoursePhp\Blog\Commands;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Exceptions\ArgumentsException;
use Melni\AdvancedCoursePhp\Exceptions\CommandException;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Person\Name;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface          $logger
    )
    {
    }

    /**
     * @throws ArgumentsException
     * @throws CommandException
     * @throws InvalidUuidException
     */
    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            return;
        }

        $password = $arguments->get('password');

        $user = User::createFrom(
            $username,
            $password,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            ),
        );

        $this->usersRepository->save($user);
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
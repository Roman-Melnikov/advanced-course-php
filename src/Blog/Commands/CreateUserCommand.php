<?php

namespace Melni\AdvancedCoursePhp\Blog\Commands;

use Melni\AdvancedCoursePhp\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\CommandException;
use Melni\AdvancedCoursePhp\UserNotFoundException;
use Melni\AdvancedCoursePhp\Person\Name;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    /**
     * @throws CommandException
     */
    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            throw new CommandException("Пользователь уже существует: $username");
        }

        $this->usersRepository->save(new User(
            UUID::random(),
            new Name($arguments->get('first_name'), $arguments->get('last_name')),
            $username,
        ));
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
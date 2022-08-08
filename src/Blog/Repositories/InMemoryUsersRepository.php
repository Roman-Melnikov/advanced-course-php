<?php

namespace Melni\AdvancedCoursePhp\Blog\Repositories;

use Melni\AdvancedCoursePhp\Blog\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;

class InMemoryUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private array $users = []
    )
    {

    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if ((string)$uuid === (string)$user->getUuid()) {
                return $user;
            }
        }
        throw new UserNotFoundException(
            'Пользователя с данным uuid: ' . (string)$uuid . ' нет'
        );
    }

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($username === $user->getUsername) {
                return $user;
            }
        }
        throw new UserNotFoundException(
            'Пользователя с таким логином: ' . $username . ' нет'
        );
    }
}
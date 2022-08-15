<?php

namespace Melni\AdvancedCoursePhp\Blog\Repositories\UsersRepository;

use Melni\AdvancedCoursePhp\Blog\Repositories\Interfaces\UsersRepositoryInterface;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\InvalidUuidException;
use Melni\AdvancedCoursePhp\UserNotFoundException;
use Melni\AdvancedCoursePhp\Person\Name;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private \PDO $pdo
    )
    {
    }

    /**
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE uuid = :uuid');
        $statement->execute([':uuid' => (string)$uuid]);

        return $this->getUser($statement, $uuid);
    }

    public function save(User $user): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users 
              (uuid, username, first_Name, last_Name)
            VALUES 
              (:uuid, :username, :firstName, :lastName)'
        );
        $statement->execute([
            ':uuid' => (string)$user->getUuid(),
            ':username' => $user->getUsername(),
            ':firstName' => $user->getName()->getFirstName(),
            ':lastName' => $user->getName()->getLastName()
        ]);
    }

    /**
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $statement->execute([':username' => $username,]);

        return $this->getUser($statement, $username);
    }

    /**
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    private function getUser(\PDOStatement $statement, string $usernameOrUUID): User
    {
        $result = $statement->fetch();

        if (!$result) {
            throw new UserNotFoundException(
                'Пользователя с uuid: ' . $usernameOrUUID . ' нет'
            );
        }
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username']
        );
    }
}
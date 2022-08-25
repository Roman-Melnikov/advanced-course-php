<?php

namespace Melni\AdvancedCoursePhp\Repositories\UsersRepository;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Person\Name;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private \PDO            $pdo,
        private LoggerInterface $logger
    )
    {
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidUuidException
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
              (uuid, username, first_Name, last_Name, password)
            VALUES 
              (:uuid, :username, :firstName, :lastName, :password)'
        );
        $statement->execute([
            ':uuid' => (string)$user->getUuid(),
            ':username' => $user->getUsername(),
            ':firstName' => $user->getName()->getFirstName(),
            ':lastName' => $user->getName()->getLastName(),
            ':password' => $user->getHashedPassword(),
        ]);

        $this->logger->info("User created: {$user->getUuid()}");
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidUuidException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $statement->execute([':username' => $username,]);

        return $this->getUser($statement, $username);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidUuidException
     */
    private function getUser(\PDOStatement $statement, string $usernameOrUUID): User
    {
        $result = $statement->fetch();

        if (!$result) {
            $message = 'No user: ' . $usernameOrUUID;

            $this->logger->warning($message);
            throw new UserNotFoundException($message);
        }
        return new User(
            new UUID($result['uuid']),
            $result['username'],
            $result['password'],
            new Name(
                $result['first_name'],
                $result['last_name']
            ),
        );
    }

    public function remove(UUID $uuid): void
    {
        $statement = $this->pdo->prepare(
            'DELETE
            FROM users 
            WHERE uuid = :uuid'
        );

        $statement->execute([':uuid' => (string)$uuid]);
        $this->logger->info("User deleted: $uuid");
    }
}
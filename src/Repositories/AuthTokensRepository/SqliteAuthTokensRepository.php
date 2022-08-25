<?php

namespace Melni\AdvancedCoursePhp\Repositories\AuthTokensRepository;

use Melni\AdvancedCoursePhp\Blog\AuthToken;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AuthTokenNotFoundException;
use Melni\AdvancedCoursePhp\Exceptions\AuthTokenRepositoryException;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\AuthTokensRepositoryInterface;

class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{

    public function __construct(
        private \PDO $pdo
    )
    {
    }

    /**
     * @throws AuthTokenRepositoryException
     */
    public function save(AuthToken $authToken): void
    {
        $query = <<<'SQL'
                INSERT INTO tokens (
                    token,
                    user_uuid,
                    expires_on
                )VALUES( 
                    :token,
                    :user_uuid,
                    :expires_on
                )
                ON CONFLICT (token) DO UPDATE SET
                    expires_on = :expires_on
        SQL;

        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute(
                [
                    ':token' => (string)$authToken,
                    ':user_uuid' => (string)$authToken->getUserUuid(),
                    'expires_on' => $authToken->getExpiresOn()
                        ->format(\DateTimeInterface::ATOM)
                ]
            );
        } catch (\PDOException $e) {
            throw new AuthTokenRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

    }

    /**
     * @throws AuthTokenRepositoryException
     * @throws AuthTokenNotFoundException
     */
    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->pdo->prepare(
                'SELECT *
            FROM tokens
            WHERE token = :token'
            );

            $statement->execute(
                [':token' => $token]
            );

            $result = $statement->fetch();
        } catch (\PDOException $e) {
            throw new AuthTokenRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

        if (!$result) {
            throw new AuthTokenNotFoundException(
                'Cannot find token: ' . $token
            );
        }

        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_uuid']),
                new \DateTimeImmutable(
                    $result['expires_on']
                )
            );
        } catch (\Exception $e) {
            throw new AuthTokenRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }
}
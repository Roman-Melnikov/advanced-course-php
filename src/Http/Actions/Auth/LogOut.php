<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Auth;

use Melni\AdvancedCoursePhp\Exceptions\AuthException;
use Melni\AdvancedCoursePhp\Exceptions\AuthTokenNotFoundException;
use Melni\AdvancedCoursePhp\Exceptions\AuthTokenRepositoryException;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\AuthTokensRepositoryInterface;

class LogOut implements ActionsInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private \PDO                          $pdo,
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }

    /**
     * @throws HttpException
     * @throws AuthException
     * @throws AuthTokenRepositoryException
     */
    public function handle(Request $request): Response
    {
        $header = $request->header('Authorization');

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException(
                "Malformed token: [$header]"
            );
        }

        $token = mb_substr(
            $header,
            strlen(self::HEADER_PREFIX)
        );

        if (!$this->authTokenExists($token)) {
            return new ErrorResponse(
                'Cannot find token: ' . $token
            );
        }

        $expired = (new \DateTimeImmutable())
            ->format(\DateTimeInterface::ATOM);

        try {
            $statement = $this->pdo->prepare(
                'UPDATE tokens
            SET expires_on = :expired
            WHERE token = :token'
            );

            $statement->execute(
                [
                    ':expired' => $expired,
                    ':token' => $token
                ]
            );
        } catch (\PDOException $e) {
            throw new AuthTokenRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

        return new SuccessFulResponse(
            ['Token expired' => "[$token]"]
        );
    }

    private function authTokenExists(string $token): bool
    {
        try {
            $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $e) {
            return false;
        }
        return true;
    }
}
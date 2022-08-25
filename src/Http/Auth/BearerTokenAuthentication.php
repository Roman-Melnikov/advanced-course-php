<?php

namespace Melni\AdvancedCoursePhp\Http\Auth;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Exceptions\AuthException;
use Melni\AdvancedCoursePhp\Exceptions\AuthTokenNotFoundException;
use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\AuthTokensRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private UsersRepositoryInterface      $usersRepository,
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException(
                "Malformed token: [$header]"
            );
        }

        $token = mb_substr(
            $header,
            strlen(self::HEADER_PREFIX)
        );

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $e) {
            throw new AuthException(
                "Bad token: [$token]"
            );
        }

        if ($authToken->getExpiresOn() <= new \DateTimeImmutable()) {
            throw new AuthException(
                "Token expired: [$token]"
            );
        }

        $userUuid = $authToken->getUserUuid();


        return $this->usersRepository->get($userUuid);
    }
}
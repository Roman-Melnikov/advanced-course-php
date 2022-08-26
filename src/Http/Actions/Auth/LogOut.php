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

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $expired = new \DateTimeImmutable();

        $authToken->setExpiresOn($expired);

        $this->authTokensRepository->save($authToken);

        return new SuccessFulResponse(
            ['Token expired' => "[$token]"]
        );
    }
}
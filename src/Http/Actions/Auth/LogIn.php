<?php

namespace Melni\AdvancedCoursePhp\Http\Actions\Auth;

use Melni\AdvancedCoursePhp\Blog\AuthToken;
use Melni\AdvancedCoursePhp\Exceptions\AuthException;
use Melni\AdvancedCoursePhp\Http\Actions\ActionsInterface;
use Melni\AdvancedCoursePhp\Http\Auth\PasswordAuthenticationInterface;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\AuthTokensRepositoryInterface;

class LogIn implements ActionsInterface
{

    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface   $authTokensRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->getUuid(),
            (new \DateTimeImmutable())->modify('+1 day')
        );

        $this->authTokensRepository->save($authToken);

        return new SuccessFulResponse(
            ['token' => (string)$authToken]
        );

    }
}
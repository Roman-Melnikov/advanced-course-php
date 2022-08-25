<?php

namespace Melni\AdvancedCoursePhp\UnitTests\Actions\Posts;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AuthException;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\CreatePost;
use Melni\AdvancedCoursePhp\Http\Auth\BearerTokenAuthentication;
use Melni\AdvancedCoursePhp\Http\Auth\JsonBodyUsernameAuthentication;
use Melni\AdvancedCoursePhp\Http\Auth\JsonBodyUuidAuthentication;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Person\Name;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\AuthTokensRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\PostsRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CreatePostTest extends TestCase
{

    public function testItReturnsSuccessAnswer(): void
    {
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $authenticationStub = $this->createStub(BearerTokenAuthentication::class);

        $authenticationStub
            ->method('user')
            ->willReturn(
                new User(
                    UUID::random(),
                    'username',
                    bin2hex(random_bytes(40)),
                    new Name('first', 'last'),
                )
            );

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $authenticationStub
        );

        $request = new Request(
            [],
            [],
            '{
                "title": "lorem",
                "text": "lorem"
                }'
        );

        $actual = $createPost->handle($request);

        $this->assertInstanceOf(
            SuccessFulResponse::class,
            $actual
        );
    }

    /**
     * @throws UserNotFoundException
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorIfAuthTokenNotFound(): void
    {
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $authenticationStub = $this->createStub(BearerTokenAuthentication::class);

        $authenticationStub
            ->method('user')
            ->willThrowException(
                new AuthException('Bad token: ecd72118-ff57-4d53-a550-b504119ee7f2')
            );

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $authenticationStub
        );

        $request = new Request(
            [],
            [],
            '{
                "token": "ecd72118-ff57-4d53-a550-b504119ee7f2"
                }'
        );

        $actual = $createPost->handle($request);
        $actual->send();

        $this->assertInstanceOf(
            ErrorResponse::class,
            $actual
        );

        $this->expectOutputString(
            '{"success":false,"reason":"Bad token: ecd72118-ff57-4d53-a550-b504119ee7f2"}'
        );
    }

    public function ArgumentsProvider(): iterable
    {
        return [
            [
                '{"text": "lorem"}',
                'title'
            ],
            [
                '{"title": "lorem"}',
                'text'
            ]
        ];
    }

    /**
     * @dataProvider ArgumentsProvider
     * @runInSeparateProcess
     * @PreserveGlobalState disabled
     */
    public function testItReturnsErrorIfNotAllParameters(
        $jsonBody,
        $param
    ): void
    {
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $authenticationStub = $this->createStub(BearerTokenAuthentication::class);

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $authenticationStub
        );

        $request = new Request(
            [],
            [],
            $jsonBody
        );

        $actual = $createPost->handle($request);

        $this->assertInstanceOf(
            ErrorResponse::class,
            $actual
        );

        $this->expectOutputString(
            '{"success":false,"reason":"No such field: ' . $param . '"}'
        );

        $actual->send();
    }
}
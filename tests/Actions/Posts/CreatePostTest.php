<?php

namespace Melni\AdvancedCoursePhp\UnitTests\Actions\Posts;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Http\Actions\Posts\CreatePost;
use Melni\AdvancedCoursePhp\Http\Auth\JsonBodyUsernameIdentification;
use Melni\AdvancedCoursePhp\Http\Auth\JsonBodyUuidIdentification;
use Melni\AdvancedCoursePhp\Http\ErrorResponse;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\SuccessFulResponse;
use Melni\AdvancedCoursePhp\Person\Name;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\PostsRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CreatePostTest extends TestCase
{

    public function testItReturnsSuccessAnswer(): void
    {
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $usersRepositoryStub = $this->createStub(UsersRepositoryInterface::class);

        $usersRepositoryStub
            ->method('getByUsername')
            ->willReturn(
                new User(
                    UUID::random(),
                    new Name('first', 'last'),
                    'username'
                )
            );

        $identification = new JsonBodyUsernameIdentification($usersRepositoryStub);

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $identification
        );

        $request = new Request(
            [],
            [],
            '{
                "username": "lorem",
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
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorIfInvalidUuid(): void
    {
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $usersRepositoryStub = $this->createStub(UsersRepositoryInterface::class);

        $usersRepositoryStub
            ->method('getByUsername')
            ->willReturn(
                new User(
                    UUID::random(),
                    new Name('first', 'last'),
                    'username'
                )
            );

        $identification = new JsonBodyUuidIdentification($usersRepositoryStub);

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $identification
        );

        $request = new Request(
            [],
            [],
            '{
                "user_uuid": "123456789"
                }'
        );

        $actual = $createPost->handle($request);
        $actual->send();

        $this->assertInstanceOf(
            ErrorResponse::class,
            $actual
        );

        $this->expectOutputString(
            '{"success":false,"reason":"Incorrect format UUID: 123456789"}'
        );
    }

    /**
     * @throws UserNotFoundException
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorIfUserNotFound(): void
    {
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $usersRepositoryStub = $this->createStub(UsersRepositoryInterface::class);

        $usersRepositoryStub
            ->method('get')
            ->willThrowException(
                new UserNotFoundException('No user: ecd72118-ff57-4d53-a550-b504119ee7f2')
            );

        $identification = new JsonBodyUuidIdentification($usersRepositoryStub);

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $identification
        );

        $request = new Request(
            [],
            [],
            '{
                "user_uuid": "ecd72118-ff57-4d53-a550-b504119ee7f2"
                }'
        );

        $actual = $createPost->handle($request);
        $actual->send();

        $this->assertInstanceOf(
            ErrorResponse::class,
            $actual
        );

        $this->expectOutputString(
            '{"success":false,"reason":"No user: ecd72118-ff57-4d53-a550-b504119ee7f2"}'
        );
    }

    public function ArgumentsProvider(): iterable
    {
        return [
            [
                '{"title": "lorem","text": "lorem"}',
                'username'
            ],
            [
                '{"username": "lorem","text": "lorem"}',
                'title'
            ],
            [
                '{"username": "lorem","title": "lorem"}',
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
        $usersRepositoryStub = $this->createStub(UsersRepositoryInterface::class);

        $identification = new JsonBodyUsernameIdentification($usersRepositoryStub);

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $identification
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
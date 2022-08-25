<?php

namespace Melni\AdvancedCoursePhp\UnitTests\Actions\Users;

use Melni\AdvancedCoursePhp\Exceptions\HttpException;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Http\Actions\Users\CreateUser;
use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use Melni\AdvancedCoursePhp\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;

class CreateUserTest extends TestCase
{
    public function testItRequiresPassword(): void
    {
        $usersRepositoryStub = $this->createStub(UsersRepositoryInterface::class);

        $usersRepositoryStub
            ->method('getByUsername')
            ->willThrowException(
                new UserNotFoundException('No user')
            );

        $action = new CreateUser(
            $usersRepositoryStub,
            new DummyLogger()
        );

        $request = new Request(
            [],
            [],
            '{
                "username": "username",
                "first_name": "first",
                "last_name": "last"
                }'
        );

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('No such field: password');

        $action->handle($request);
    }
}
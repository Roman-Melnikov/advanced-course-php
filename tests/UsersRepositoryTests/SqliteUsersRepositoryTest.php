<?php

namespace Melni\AdvancedCoursePhp\UnitTests\UsersRepositoryTests;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Person\Name;
use Melni\AdvancedCoursePhp\Repositories\UsersRepository\SqliteUsersRepository;
use Melni\AdvancedCoursePhp\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{
    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '104e8613-b7b2-4cb9-8296-56a765033ff8',
                ':username' => 'username',
                ':password' => 'password',
                ':firstName' => 'first',
                ':lastName' => 'last'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $userRepository = new SqliteUsersRepository($connectionStub, new DummyLogger());

        $user = new User(
            new UUID('104e8613-b7b2-4cb9-8296-56a765033ff8'),
            'username',
            'password',
            new Name('first', 'last'),
        );

        $userRepository->save($user);
    }

    /**
     * @throws \Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException
     * @throws UserNotFoundException
     */
    public function testItGetUserByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '104e8613-b7b2-4cb9-8296-56a765033ff8',
            'first_name' => 'first',
            'last_name' => 'last',
            'username' => 'username',
            'password' => 'password'
        ]);

        $userRepository = new SqliteUsersRepository($connectionStub, new DummyLogger());
        $user = $userRepository->get(new UUID('104e8613-b7b2-4cb9-8296-56a765033ff8'));

        $this->assertSame('104e8613-b7b2-4cb9-8296-56a765033ff8', (string)$user->getUuid());
    }

    /**
     * @throws \Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException
     */
    public function testItThrowAnExceptionWhenUserNotFound(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $statementMock->method('fetch')->willReturn(false);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('No user: 9dba7ab0-93be-4ff4-9699-165320c97694');

        $userRepository = new SqliteUsersRepository($connectionStub, new DummyLogger());
        $userRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));
    }
}
<?php

namespace Melni\AdvancedCoursePhp\UnitTests\CommentsRepositoryTests;

use Melni\AdvancedCoursePhp\Blog\Comment;
use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\CommentNotFoundException;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Exceptions\PostNotFoundException;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Person\Name;
use Melni\AdvancedCoursePhp\Repositories\CommentsRepository\SqliteCommentsRepository;
use Melni\AdvancedCoursePhp\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItSavesCommentsToDatabase(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '9dba7ab0-93be-4ff4-9699-165320c97694',
                ':txt' => 'Из предыдущей главы уже видно',
                ':user_uuid' => '104e8613-b7b2-4cb9-8296-56a765033ff8',
                ':post_uuid' => '7bd053ac-6dfb-46ac-908a-35222a579851'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $commentRepository = new SqliteCommentsRepository($connectionStub, new DummyLogger());

        $user = new User(
            new UUID('104e8613-b7b2-4cb9-8296-56a765033ff8'),
            'username',
            bin2hex(random_bytes(40)),
            new Name('first', 'last'),
        );

        $post = new Post(
            new UUID('7bd053ac-6dfb-46ac-908a-35222a579851'),
            $user,
            'Пожалуй, я',
            'Из предыдущей главы уже видно'
        );

        $comment = new Comment(
            new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'),
            $user,
            $post,
            'Из предыдущей главы уже видно'
        );

        $commentRepository->save($comment);
    }

    public function testItGetCommentByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $statementMock->method('fetch')->willReturn([
            'user_uuid' => '104e8613-b7b2-4cb9-8296-56a765033ff8',
            'first_name' => 'first',
            'last_name' => 'last',
            'username' => 'username',
            'password' => 'password',
            'post_uuid' => '10418021-7cc6-4221-a1e8-29bfac1b4d20',
            'title' => 'title',
            'text' => 'text',
            'txt' => 'txt'
        ]);

        $commentRepository = new SqliteCommentsRepository($connectionStub, new DummyLogger());
        $comment = $commentRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));

        $this->assertSame('9dba7ab0-93be-4ff4-9699-165320c97694', (string)$comment->getUuid());
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    public function testItThrowAnExceptionWhenCommentNotFound(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $statementMock->method('fetch')->willReturn(false);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('No comment: 9dba7ab0-93be-4ff4-9699-165320c97694');

        $commentRepository = new SqliteCommentsRepository($connectionStub, new DummyLogger());
        $commentRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));
    }
}
<?php

namespace Melni\AdvancedCoursePhp\UnitTests\PostsRepositoryTests;

use Melni\AdvancedCoursePhp\Blog\Exceptions\PostNotFoundException;
use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Person\Name;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '9dba7ab0-93be-4ff4-9699-165320c97694',
                ':title' => 'Пожалуй, я',
                ':text' => 'Из предыдущей главы уже видно',
                ':user_uuid' => '104e8613-b7b2-4cb9-8296-56a765033ff8'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostsRepository($connectionStub);

        $user = new User(
            new UUID('104e8613-b7b2-4cb9-8296-56a765033ff8'),
            new Name('first', 'last'),
            'username'
        );

        $post = new Post(
            new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'),
            $user,
            'Пожалуй, я',
            'Из предыдущей главы уже видно'
        );

        $postRepository->save($post);
    }

    /**
     * @throws \Melni\AdvancedCoursePhp\Blog\Exceptions\PostNotFoundException
     * @throws \Melni\AdvancedCoursePhp\Blog\Exceptions\InvalidUuidException
     */
    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $statementMock->method('fetch')->willReturn([
            'user_uuid' => '104e8613-b7b2-4cb9-8296-56a765033ff8',
            'first_name' => 'first',
            'last_name' => 'last',
            'username' => 'username',
            'title' => 'title',
            'text' => 'text'
        ]);

        $postRepository = new SqlitePostsRepository($connectionStub);
        $post = $postRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));

        $this->assertSame('9dba7ab0-93be-4ff4-9699-165320c97694', (string)$post->getUuid());
    }

    public function testItThrowAnExceptionWhenPostNotFound(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $statementMock->method('fetch')->willReturn(false);

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Поста с uuid: 9dba7ab0-93be-4ff4-9699-165320c97694 нет');

        $postRepository = new SqlitePostsRepository($connectionStub);
        $postRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));
    }
}
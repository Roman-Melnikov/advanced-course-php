<?php

namespace Melni\AdvancedCoursePhp\Blog\Repositories\PostsRepository;

use Melni\AdvancedCoursePhp\Blog\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Blog\Exceptions\PostNotFoundException;
use Melni\AdvancedCoursePhp\Blog\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\Repositories\Interfaces\PostsRepositoryInterface;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Person\Name;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private \PDO $pdo
    )
    {

    }

    public function save(Post $post): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO posts
                (uuid, title, text, user_uuid)
                VALUES 
                    (:uuid, :title, :text, :user_uuid)'
        );
        $statement->execute([
            ':uuid' => (string)$post->getUuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
            ':user_uuid' => (string)$post->getAutor()->getUuid()
        ]);
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): Post
    {
        $postStatement = $this->query('posts', $uuid);
        $postResult = $postStatement->fetch();

        if (!$postResult) {
            throw new PostNotFoundException(
                'Поста с uuid: ' . $uuid . ' нет'
            );
        }

        $userStatement = $this->query('users', new UUID($postResult['user_uuid']));
        $userResult = $userStatement->fetch();

        if (!$userResult) {
            throw new UserNotFoundException(
                'Пользователя с uuid: ' . $postResult['user_uuid'] . ' нет'
            );
        }

        return new Post(
            $uuid,
            new User(
                new UUID($postResult['user_uuid']),
                new Name($userResult['first_name'], $userResult['last_name']),
                $userResult['username']
            ),
            $postResult['title'],
            $postResult['text']);
    }

    private function query(string $table, UUID $uuid): \PDOStatement
    {
        $statement = $this->pdo->prepare(
            "SELECT *
                   FROM $table
                   WHERE $table.uuid = :uuid"
        );
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);
        return $statement;
    }
}
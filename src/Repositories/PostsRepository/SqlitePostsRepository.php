<?php

namespace Melni\AdvancedCoursePhp\Repositories\PostsRepository;

use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AppException;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Exceptions\PostNotFoundException;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Person\Name;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\PostsRepositoryInterface;
use Psr\Log\LoggerInterface;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private \PDO            $pdo,
        private LoggerInterface $logger
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

        $this->logger->info("Post created: {$post->getUuid()}");
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidUuidException
     */
    public function get(UUID $uuid): Post
    {
        $postResult = $this->query(
            'posts',
            $uuid,
        );

        if (!$postResult) {
            $message = "No post: $uuid";

            $this->logger->warning($message);
            throw new PostNotFoundException($message);
        }

        $userUuid = new UUID($postResult['user_uuid']);

        $userResult = $this->query(
            'users',
            $userUuid,
        );

        return new Post(
            $uuid,
            new User(
                $userUuid,
                new Name($userResult['first_name'], $userResult['last_name']),
                $userResult['username']
            ),
            $postResult['title'],
            $postResult['text']);
    }

    private function query(string $table, UUID $uuid,): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT *
                   FROM $table
                   WHERE $table.uuid = :uuid"
        );
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        return $statement->fetch() ?: null;
    }

    public function remove(UUID $uuid): void
    {
        $statement = $this->pdo->prepare(
            'DELETE
                   FROM posts
                   WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => $uuid
        ]);
    }
}
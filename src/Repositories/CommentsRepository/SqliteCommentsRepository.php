<?php

namespace Melni\AdvancedCoursePhp\Repositories\CommentsRepository;

use Melni\AdvancedCoursePhp\Blog\Comment;
use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\AppException;
use Melni\AdvancedCoursePhp\Exceptions\CommentNotFoundException;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Exceptions\PostNotFoundException;
use Melni\AdvancedCoursePhp\Exceptions\UserNotFoundException;
use Melni\AdvancedCoursePhp\Person\Name;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\CommentsRepositoryInterface;
use Psr\Log\LoggerInterface;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    public function __construct(
        private \PDO            $pdo,
        private LoggerInterface $logger
    )
    {
    }

    public function save(Comment $comment): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO comments
                (uuid, txt, user_uuid, post_uuid)
                VALUES 
                    (:uuid, :txt, :user_uuid, :post_uuid)'
        );
        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':txt' => $comment->getText(),
            ':user_uuid' => $comment->getUser()->getUuid(),
            ':post_uuid' => $comment->getPost()->getUuid(),
        ]);

        $this->logger->info("Comment created: {$comment->getUuid()}");
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidUuidException
     */
    public function get(UUID $uuid): Comment
    {
        $commentResult = $this->query(
            'comments',
            $uuid,
        );

        if (!$commentResult) {
            $message = "No comment: $uuid";

            $this->logger->warning($message);
            throw new CommentNotFoundException($message);
        }

        $postUuid = new UUID($commentResult['post_uuid']);
        $userUuid = new UUID($commentResult['user_uuid']);

        $postResult = $this->query(
            'posts',
            $postUuid,
        );

        $userResult = $this->query(
            'users',
            $userUuid,
        );

        $user = new User(
            new UUID($commentResult['user_uuid']),
            $userResult['username'],
            $userResult['password'],
            new Name(
                $userResult['first_name'],
                $userResult['last_name']
            ),
        );

        $post = new Post(
            new UUID($commentResult['post_uuid']),
            $user,
            $postResult['title'],
            $postResult['text']
        );

        return new Comment(
            $uuid,
            $user,
            $post,
            $commentResult['txt']
        );
    }

    private function query(string $table, UUID $uuid): ?array
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
                   FROM comments
                   WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => $uuid
        ]);
    }
}
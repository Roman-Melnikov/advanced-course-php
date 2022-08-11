<?php

namespace Melni\AdvancedCoursePhp\Blog\Repositories\CommentsRepository;

use Melni\AdvancedCoursePhp\Blog\Comment;
use Melni\AdvancedCoursePhp\Blog\Exceptions\CommentNotFoundException;
use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\Repositories\Interfaces\CommentsRepositoryInterface;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Person\Name;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    public function __construct(
        private \PDO $pdo
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
    }

    /**
     * @throws CommentNotFoundException
     * @throws \Melni\AdvancedCoursePhp\Blog\Exceptions\InvalidUuidException
     */
    public function get(UUID $uuid): Comment
    {
        $statement = $this->pdo->prepare(
            'SELECT *
                    FROM users
                    LEFT JOIN posts
                    ON users.uuid = posts.user_uuid
                    LEFT JOIN comments
                    ON posts.uuid = comments.post_uuid
                    WHERE comments.uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        $result = $statement->fetch();

        if (!$result) {
            throw new CommentNotFoundException(
                'Комментария с uuid: ' . $uuid . ' нет'
            );
        }

        $user = new User(
            new UUID($result['user_uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username']
        );

        $post = new Post(
            new UUID($result['post_uuid']),
            $user,
            $result['title'],
            $result['text']
        );

        return new Comment(
            $uuid,
            $user,
            $post,
            $result['txt']
        );
    }
}
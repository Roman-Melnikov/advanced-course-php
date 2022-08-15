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
     * @throws PostNotFoundException
     * @throws CommentNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidUuidException
     * @throws AppException
     */
    public function get(UUID $uuid): Comment
    {
        $commentResult = $this->query(
            'comments',
            $uuid,
            new CommentNotFoundException("Комментария с uuid: $uuid нет")
        );

        $postUuid = new UUID($commentResult['post_uuid']);
        $userUuid = new UUID($commentResult['user_uuid']);

        $postResult = $this->query(
            'posts',
            $postUuid,
            new PostNotFoundException('Поста с uuid: ' . $commentResult['post_uuid'] . ' нет')
        );

        $userResult = $this->query(
            'users',
            $userUuid,
            new UserNotFoundException('Пользователя с uuid: ' . $commentResult['user_uuid'] . ' нет')
        );

        $user = new User(
            new UUID($commentResult['user_uuid']),
            new Name($userResult['first_name'], $userResult['last_name']),
            $userResult['username']
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

    /**
     * @throws AppException
     */
    private function query(
        string       $table,
        UUID         $uuid,
        AppException $exceptionName
    ): array
    {
        $statement = $this->pdo->prepare(
            "SELECT *
                   FROM $table
                   WHERE $table.uuid = :uuid"
        );
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        $result = $statement->fetch();

        if (!$result) {
            throw $exceptionName;
        }

        return $result;
    }

    /**
     * @throws CommentNotFoundException
     * @throws PostNotFoundException
     * @throws InvalidUuidException
     * @throws AppException
     * @throws UserNotFoundException
     */
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
<?php

namespace Melni\AdvancedCoursePhp\Blog\Repositories\CommentsRepository;

use Melni\AdvancedCoursePhp\Blog\Comment;
use Melni\AdvancedCoursePhp\Blog\Exceptions\CommentNotFoundException;
use Melni\AdvancedCoursePhp\Blog\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Blog\Exceptions\PostNotFoundException;
use Melni\AdvancedCoursePhp\Blog\Exceptions\UserNotFoundException;
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
     * @throws InvalidUuidException
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): Comment
    {
        $commentStatement = $this->query('comments', $uuid);
        $commentResult = $commentStatement->fetch();

        if (!$commentResult) {
            throw new CommentNotFoundException(
                'Комментария с uuid: ' . $uuid . ' нет'
            );
        }

        $postStatement = $this->query('posts', new UUID($commentResult['post_uuid']));
        $postResult = $postStatement->fetch();

        if (!$postResult) {
            throw new PostNotFoundException(
                'Поста с uuid: ' . $commentResult['post_uuid'] . ' нет'
            );
        }

        $userStatement = $this->query('users', new UUID($commentResult['user_uuid']));
        $userResult = $userStatement->fetch();

        if (!$userResult) {
            throw new UserNotFoundException(
                'Пользователя с uuid: ' . $commentResult['user_uuid'] . ' нет'
            );
        }

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

    public function query(string $table, UUID $uuid): \PDOStatement
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
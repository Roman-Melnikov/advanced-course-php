<?php

namespace Melni\AdvancedCoursePhp\Blog\Repositories;

use Melni\AdvancedCoursePhp\Blog\Exceptions\PostNotFoundException;
use Melni\AdvancedCoursePhp\Blog\Post;
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
                (uuid, heading, text, user_uuid)
                VALUES 
                    (:uuid, :heading, :text, :user_uuid)'
        );
        $statement->execute([
            ':uuid' => (string)$post->getUuid(),
            ':heading' => $post->getHeading(),
            ':text' => $post->getText(),
            ':user_uuid' => (string)$post->getAutor()->getUuid()
        ]);
    }

    /**
     * @throws PostNotFoundException
     * @throws \Melni\AdvancedCoursePhp\Blog\Exceptions\InvalidUuidException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->pdo->prepare(
            'SELECT *
                    FROM posts LEFT JOIN users
                    ON posts.user_uuid = users.uuid 
                    WHERE posts.uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        $result = $statement->fetch();

        if (!$result) {
            throw new PostNotFoundException(
                'Поста с uuid: ' . (string)$uuid . 'нет'
            );
        }

        return new Post(
            $uuid,
            new User(
                new UUID($result['user_uuid']),
                new Name($result['first_name'], $result['last_name']),
                $result['username']
            ),
            $result['heading'],
            $result['text']);
    }
}
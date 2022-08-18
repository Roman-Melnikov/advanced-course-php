<?php

namespace Melni\AdvancedCoursePhp\Repositories\LikesRepository;

use Melni\AdvancedCoursePhp\Blog\Like;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\LikeAlreadyExists;
use Melni\AdvancedCoursePhp\Exceptions\LikeNotFoundException;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\PostsLikesRepositoryInterface;

class SqlitePostsLikesRepository implements PostsLikesRepositoryInterface
{

    public function __construct(
        private \PDO $pdo
    )
    {
    }

    public function save(Like $like): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO postsLikes
                       (uuid, post_uuid, user_uuid)
                    VALUES 
                       (:uuid, :post_uuid, :user_uuid)'
        );
        $statement->execute(
            [
                ':uuid' => $like,
                'post_uuid' => $like->getPost()->getUuid(),
                'user_uuid' => $like->getUser()->getUuid()
            ]
        );
    }

    /**
     * @throws LikeNotFoundException
     */
    public function getByPostUuid(UUID $uuid): array
    {
        $statement = $this->pdo->prepare(
            'SELECT *
            FROM postsLikes
            WHERE post_uuid = :uuid'
        );
        $statement->execute([':uuid' => $uuid]);

        $result = $statement->fetchAll();

        if (!$result) {
            throw new LikeNotFoundException(
                'No likes to this post: ' . $uuid
            );
        }

        return $result;
    }

    /**
     * @throws LikeAlreadyExists
     */
    public function checkUserLikeForPostExists($postUuid, $userUuid): void
    {
        $statement = $this->pdo->prepare(
            'SELECT *
            FROM postsLikes
            WHERE 
                post_uuid = :postUuid AND user_uuid = :userUuid'
        );

        $likeExists = $statement->execute(
            [
                ':postUuid' => $postUuid,
                ':userUuid' => $userUuid
            ]
        );

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExists(
                'The users like for this post already exists'
            );
        }
    }

    public function remove(UUID $uuid): void
    {
        $statement = $this->pdo->prepare(
            'DELETE
                   FROM postsLikes
                   WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => $uuid
        ]);
    }
}
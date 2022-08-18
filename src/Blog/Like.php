<?php

namespace Melni\AdvancedCoursePhp\Blog;

class Like
{
    public function __construct(
        private UUID $uuid,
        private Post $post,
        private User $user
    )
    {
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function __toString(): string
    {
        return (string)$this->getUuid();
    }
}
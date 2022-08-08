<?php

namespace Melni\AdvancedCoursePhp\Blog;

class Comment
{
    public function __construct(
        private UUID   $uuid,
        private User   $user,
        private Post   $post,
        private string $txt
    )
    {
    }

    public function __toString(): string
    {
        return $this->getText();
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return \Melni\AdvancedCoursePhp\Blog\User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return \Melni\AdvancedCoursePhp\Blog\Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->txt;
    }
}
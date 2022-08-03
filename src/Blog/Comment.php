<?php

namespace Melni\AdvancedCoursePhp\Blog;

use Melni\AdvancedCoursePhp\Blog\{User, Post};

class Comment
{
    private int $id;
    private int $user_id;
    private int $post_id;
    private string $text;

    /**
     * @param int $id
     * @param int $user_id
     * @param int $post_id
     * @param string $text
     */
    public function __construct(int $id, User $user, Post $post, string $text)
    {
        $this->id = $id;
        $this->user_id = $user->getId();
        $this->post_id = $post->getId();
        $this->text = $text;
    }

    public function __toString(): string
    {
        return $this->getText();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->post_id;
    }

    /**
     * @param int $post_id
     */
    public function setPostId(int $post_id): void
    {
        $this->post_id = $post_id;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
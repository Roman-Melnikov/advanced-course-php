<?php

namespace Melni\AdvancedCoursePhp\Blog\Likes;

use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;

class PostLike extends Like
{
    private Post $post;

    public function __construct(UUID $uuid, User $user, Post $post)
    {
        parent::__construct($uuid, $user);
        $this->post = $post;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }
}
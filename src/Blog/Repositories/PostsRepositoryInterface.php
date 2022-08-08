<?php

namespace Melni\AdvancedCoursePhp\Blog\Repositories;

use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;

    public function get(UUID $uuid): Post;
}
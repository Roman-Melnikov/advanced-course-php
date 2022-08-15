<?php

namespace Melni\AdvancedCoursePhp\Repositories\Interfaces;

use Melni\AdvancedCoursePhp\Blog\Comment;
use Melni\AdvancedCoursePhp\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;

    public function get(UUID $uuid): Comment;
}
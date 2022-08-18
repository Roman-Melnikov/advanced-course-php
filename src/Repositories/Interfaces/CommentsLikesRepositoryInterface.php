<?php

namespace Melni\AdvancedCoursePhp\Repositories\Interfaces;

use Melni\AdvancedCoursePhp\Blog\Likes\Like;
use Melni\AdvancedCoursePhp\Blog\UUID;

interface CommentsLikesRepositoryInterface
{
    public function save(Like $like): void;

    public function getByCommentUuid(UUID $uuid): array;
}
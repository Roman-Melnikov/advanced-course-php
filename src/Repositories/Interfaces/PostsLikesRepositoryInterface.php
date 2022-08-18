<?php

namespace Melni\AdvancedCoursePhp\Repositories\Interfaces;

use Melni\AdvancedCoursePhp\Blog\Like;
use Melni\AdvancedCoursePhp\Blog\UUID;

interface PostsLikesRepositoryInterface
{
    public function save(Like $like): void;

    public function getByPostUuid(UUID $uuid): array;
}
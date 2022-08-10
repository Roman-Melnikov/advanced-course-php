<?php

namespace Melni\AdvancedCoursePhp\Blog\Repositories;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;

interface UsersRepositoryInterface
{
    public function get(UUID $uuid): User;
    public function save(User $user): void;
    public function getByUsername(string $username): User;
}
<?php

namespace Melni\AdvancedCoursePhp\Repositories\Interfaces;

use Melni\AdvancedCoursePhp\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function save(AuthToken $authToken): void;

    public function get(string $token): AuthToken;
}
<?php

namespace Melni\AdvancedCoursePhp\Http\Auth;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Http\Request;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}
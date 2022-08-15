<?php

namespace Melni\AdvancedCoursePhp\Http\Actions;

use Melni\AdvancedCoursePhp\Http\Request;
use Melni\AdvancedCoursePhp\Http\Response;

interface ActionsInterface
{
    public function handle(Request $request): Response;
}
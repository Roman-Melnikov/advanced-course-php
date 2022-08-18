<?php

namespace Melni\AdvancedCoursePhp\Blog\Likes;

use Melni\AdvancedCoursePhp\Blog\Comment;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;

class CommentLike extends Like
{
    private Comment $comment;

    public function __construct(UUID $uuid, User $user, Comment $comment)
    {
        parent::__construct($uuid, $user);
        $this->comment = $comment;
    }

    /**
     * @return Comment
     */
    public function getComment(): Comment
    {
        return $this->comment;
    }
}
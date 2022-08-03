<?php

namespace Melni\AdvancedCoursePhp\Blog;

use Melni\AdvancedCoursePhp\Blog\User;

class Post
{
    private int $id;
    private int $user_id;
    private string $heading;
    private string $text;

    /**
     * @param int $id
     * @param int $user_id
     * @param string $heading
     * @param string $text
     */
    public function __construct(int $id, User $user, string $heading, string $text)
    {
        $this->id = $id;
        $this->user_id = $user->getId();
        $this->heading = $heading;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return $this->getHeading() . PHP_EOL . $this->getText();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getHeading(): string
    {
        return $this->heading;
    }

    /**
     * @param string $heading
     */
    public function setHeading(string $heading): void
    {
        $this->heading = $heading;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
<?php

namespace Melni\AdvancedCoursePhp\Blog;

use Melni\AdvancedCoursePhp\Person\Name;

class User
{
    public function __construct(
        private UUID   $uuid,
        private Name   $name,
        private string $username
    )
    {
    }

    public function __toString(): string
    {
        return $this->getName() . ' ' . $this->getUsername();
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
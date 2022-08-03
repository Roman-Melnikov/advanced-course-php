<?php

namespace Melni\AdvancedCoursePhp\Blog;

class User
{
    private int $id;
    private string $name;
    private string $surname;

    /**
     * @param int $id
     * @param string $name
     * @param string $surname
     */
    public function __construct(int $id, string $name, string $surname)
    {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
    }

    public function __toString(): string
    {
        return $this->getName() . ' ' . $this->getSurname();
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }
}
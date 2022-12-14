<?php

namespace Melni\AdvancedCoursePhp\Blog;


use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;

class UUID
{
    /**
     * @throws InvalidUuidException
     */
    public function __construct(
        private string $uuidString
    )
    {
        if (!uuid_is_valid($this->uuidString)) {
            throw new InvalidUuidException(
                "Incorrect format UUID: $this->uuidString"
            );
        }
    }

    /**
     * @throws InvalidUuidException
     */
    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

    public function __toString(): string
    {
        return $this->uuidString;
    }
}
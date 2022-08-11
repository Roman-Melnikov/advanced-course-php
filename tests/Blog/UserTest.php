<?php

namespace Melni\AdvancedCoursePhp\UnitTests\Blog;

use Melni\AdvancedCoursePhp\Blog\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Person\Name;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @throws InvalidUuidException
     */
    public function testItReturnsUserAsString(): void
    {
        $user = new User(
            new UUID(uuid_create(UUID_TYPE_RANDOM)),
            new Name('first', 'last'),
            'username'
        );

        $value = (string)$user;
        $this->assertIsString($value);
    }
}
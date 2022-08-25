<?php

namespace Melni\AdvancedCoursePhp\UnitTests\Blog;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
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
            'username',
            bin2hex(random_bytes(40)),
            new Name('first', 'last'),
        );

        $value = (string)$user;
        $this->assertIsString($value);
    }
}
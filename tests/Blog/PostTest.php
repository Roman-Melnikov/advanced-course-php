<?php

namespace Melni\AdvancedCoursePhp\UnitTests\Blog;

use Melni\AdvancedCoursePhp\Blog\Post;
use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Exceptions\InvalidUuidException;
use Melni\AdvancedCoursePhp\Person\Name;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    /**
     * @throws InvalidUuidException
     * @throws \Exception
     */
    public function testItReturnsPostAsString(): void
    {
        $user = new User(
            new UUID(uuid_create(UUID_TYPE_RANDOM)),
            'username',
            bin2hex(random_bytes(40)),
            new Name('first', 'last'),
        );

        $post = new Post(
            new UUID(uuid_create(UUID_TYPE_RANDOM)),
            $user,
            'title',
            'text'
        );

        $value = (string)$post;
        $this->assertIsString($value);
    }
}
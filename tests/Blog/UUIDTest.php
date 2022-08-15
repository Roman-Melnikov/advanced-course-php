<?php

namespace Melni\AdvancedCoursePhp\UnitTests\Blog;

use Melni\AdvancedCoursePhp\InvalidUuidException;
use Melni\AdvancedCoursePhp\Blog\UUID;
use PHPUnit\Framework\TestCase;

class UUIDTest extends TestCase
{
    public function testItThrowAnExceptionWhenBagFormatUuid(): void
    {
        $value = 'a3a17b83c9164932b1c2';

        $this->expectException(InvalidUuidException::class);
        $this->expectExceptionMessage('Неправильный формат UUID: a3a17b83c9164932b1c2');

        new UUID($value);
    }

    /**
     * @throws InvalidUuidException
     */
    public function testItReturnsRandomUuidRequiredFormat(): void
    {
        $uuid = UUID::random();

        $this->assertStringMatchesFormat('%x-%x-%x-%x-%x', $uuid);
    }
}
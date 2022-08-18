<?php

namespace Melni\AdvancedCoursePhp\UnitTests\Container;

use Melni\AdvancedCoursePhp\Container\DIContainer;
use Melni\AdvancedCoursePhp\Container\NotFoundException;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use Melni\AdvancedCoursePhp\Repositories\UsersRepository\SqliteUsersRepository;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
{
    public function testItThrowAnExceptionIfCannotType(): void
    {
        $container = new DIContainer();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: Melni\AdvancedCoursePhp\UnitTests\Container\SomeClass'
        );

        $container->get('Melni\AdvancedCoursePhp\UnitTests\Container\SomeClass');
    }

    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = new DIContainer();
        $actual = $container->get(SomeClassWithoutDependencies::class);

        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $actual
        );
    }

    public function testItResolvesClassByContract(): void
    {
        $container = new DIContainer();

        $container->bind(
            UsersRepositoryInterface::class,
            SqliteUsersRepository::class
        );

        $container->bind(
            \PDO::class,
            $this->createStub(\PDO::class)
        );

        $actual = $container->get(UsersRepositoryInterface::class);

        $this->assertInstanceOf(
            SqliteUsersRepository::class,
            $actual
        );
    }

    public function testItReturnsPredefinedObject(): void
    {
        $container = new DIContainer();

        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(7)
        );

        $actual = $container->get(SomeClassWithParameter::class);

        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $actual
        );

        $this->assertEquals(
            7,
            $actual->getValue()
        );
    }

    public function testItResolvesClassWithDependencies(): void
    {
        $container = new DIContainer();

        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(7)
        );

        $actual = $container->get(ClassWithDependencies::class);

        $this->assertInstanceOf(
            ClassWithDependencies::class,
            $actual
        );
    }
}
<?php

namespace Melni\AdvancedCoursePhp\UnitTests\Container;

class ClassWithDependencies
{
    public function __construct(
        private SomeClassWithoutDependencies $classWithoutDependencies,
        private SomeClassWithParameter $parameter
    )
    {
    }

    /**
     * @return SomeClassWithoutDependencies
     */
    public function getClassWithoutDependencies(): SomeClassWithoutDependencies
    {
        return $this->classWithoutDependencies;
    }

    /**
     * @return SomeClassWithParameter
     */
    public function getParameter(): SomeClassWithParameter
    {
        return $this->parameter;
    }
}
<?php

namespace Melni\AdvancedCoursePhp\UnitTests\Container;

class SomeClassWithParameter
{
    public function __construct(
        private int $value
    )
    {
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
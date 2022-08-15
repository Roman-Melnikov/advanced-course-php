<?php

namespace Melni\AdvancedCoursePhp\Http;

class ErrorResponse extends Response
{
    protected const SUCCESS = false;

    public function __construct(
        private string $reason = 'Something goes wrong'
    )
    {
    }

    public function payload(): array
    {
        return ['reason' => $this->reason];
    }
}
<?php

namespace FluxEcoType;

final readonly class FluxEcoBasicCredentials
{
    private function __construct(
        public string $userName,
        public string $password
    )
    {

    }

    public static function new(
        string $userName,
        string $password
    )
    {
        return new self(
            ...get_defined_vars()
        );
    }
}
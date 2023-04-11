<?php

final readonly class Config
{
    private function __construct(
        public int $serverSystemUserId
    )
    {

    }

    public static function new()
    {
        return new self(
            getenv('FLUX_ECO_SYSTEM_USER_ID') ?: 1985,
        );
    }
}
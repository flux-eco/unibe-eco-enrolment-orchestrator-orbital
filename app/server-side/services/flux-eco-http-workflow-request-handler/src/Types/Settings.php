<?php

namespace FluxEco\HttpWorkflowRequestHandler\Types;

final readonly class Settings
{
    private function __construct(
        public string $transactionIdCookieName
    )
    {

    }

    public static function new(
        string $transactionIdCookieName
    ): self
    {
        return new self(
            ...get_defined_vars()
        );
    }
}
<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Configs;


final readonly class Config
{

    private function __construct(
        public Settings $settings,
    )
    {
    }

    public static function new(
        Settings $settings,
    ): self
    {
        return new self(
            ...get_defined_vars()
        );
    }
}
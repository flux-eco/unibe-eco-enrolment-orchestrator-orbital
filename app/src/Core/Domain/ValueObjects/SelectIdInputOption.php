<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;


final readonly class SelectIdInputOption
{
    private function __construct(
        public string $id
    )
    {

    }

    public static function new(
        string $id
    ): self
    {
        return new self(...get_defined_vars());
    }
}
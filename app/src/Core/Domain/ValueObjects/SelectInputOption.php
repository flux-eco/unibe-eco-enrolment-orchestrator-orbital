<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;


final readonly class SelectInputOption
{
    private function __construct(
        public int   $id,
        public array $label
    )
    {

    }

    public static function new(
        int   $id,
        array $label
    ): self
    {
        return new self(...get_defined_vars());
    }
}
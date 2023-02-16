<?php

namespace UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ReferenceObjects;

use UnibeEco\EnrolmentOrchestratorOrbital\Core\Domain\ValueObjects;

final readonly class Country
{
    private function __construct(
        public string             $id,
        public ValueObjects\Label $label,
        public bool               $required
    )
    {

    }

    public static function new(
        string             $id,
        ValueObjects\Label $label,
        bool               $required = false
    ): self
    {
        return new self($id, $label, $required);
    }
}